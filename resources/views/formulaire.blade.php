@extends('welcome')
@section('title')Déclaration d'Accident - CNSR @endsection
@section('extra-style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<link rel="stylesheet" href="{{ asset('css/formulaire.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="header">
        <div class="logo-container">
            <img src="{{ asset('image\cnsr.jpg') }}" alt="CNSR Logo" class="logo">
            <div class="header-text">
                <h1><i class="fas fa-car-crash"></i> Formulaire de Déclaration d'Accident</h1>
                <p>Centre National de Sécurité Routière</p>
            </div>
        </div>
    </div>
    
    <div class="form-container">
        @if(session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form id="avp-form" action="{{ route('accidents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Champs cachés pour la localisation -->
            <input type="hidden" name="latitude" id="latitude-input">
            <input type="hidden" name="longitude" id="longitude-input">
            <input type="hidden" name="altitude" id="altitude-input">
            <input type="hidden" name="precision" id="precision-input">
            
            <div class="section">
                <h2 class="section-title"><i class="fas fa-map-marker-alt"></i> Déclarez l'accident au CNSR </h2>
                
                <div class="input-group">
                   
                    <select name="commune" id="commune" class="form-control" required>
                        <option value="" disabled selected>-- Sélectionnez une Commune --</option>
                        @foreach ($departements as $departement => $communes)
                            <optgroup label="{{ $departement }}">
                                @foreach ($communes as $commune)
                                    <option value="{{ $commune }}">{{ $commune }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                
                <div class="input-group">
                    <label for="lieu">Lieu de l'accident</label>
                    <textarea id="lieu" name="lieu" placeholder="Indiquez en quelques mots le lieu de l'accident." required></textarea>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title"><i class="fas fa-car"></i> Informations sur l'accident</h2>


                <label for="nombre_vehicules">Nombre de véhicules impliqués (moto,vélo et tricycle y compris)</label>

                <div class="input-group col-md-6">
                  
                        <input type="number" name="nombre_vehicules" id="nombre_vehicules" 
                        class="form-control" min="1" value="{{ old('nombre_vehicules', 1) }}" required>
                    
                </div>
            </div>
            
            <div class="section">
                <h2 class="section-title"><i class="fas fa-camera"></i> Photos de l'accident</h2>
                <p>Ajoutez des photos de l'accident pour documenter les faits</p>
                
                <div class="photo-upload-container" id="photos-container">
                    <!-- Les champs de photos seront ajoutés ici dynamiquement -->
                </div>
                
                <button type="button" class="btn btn-add" id="open-camera">
                    <i class="fas fa-camera"></i> Prendre une photo
                </button>
            </div>
            
            <div class="location-info">
                <p><i class="fas fa-info-circle"></i> Votre position sera automatiquement enregistrée lors de l'envoi du formulaire</p>
                <p id="location-status">En attente de la localisation...</p>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <button type="reset" class="btn btn-danger">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Envoyer le rapport
                </button>
            </div>
        </form>
    </div>
    
    <div class="footer">
        <p>©2025 Centre National de Sécurité Routière | <a href="#">Politique de confidentialité</a> | <a href="#">Conditions d'utilisation</a></p>
    </div>
</div>

<!-- Modal pour l'appareil photo -->
<div class="camera-modal" id="camera-modal">
    <div class="camera-container">
        <video id="camera-video" autoplay playsinline></video>
        <div class="camera-controls">
            <button type="button" class="btn btn-danger" id="close-camera">
                <i class="fas fa-times"></i> Annuler
            </button>
            <button type="button" class="btn btn-primary" id="capture-photo">
                <i class="fas fa-camera"></i> Prendre la photo
            </button>
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const photosContainer = document.getElementById('photos-container');
        const openCameraButton = document.getElementById('open-camera');
        const cameraModal = document.getElementById('camera-modal');
        const closeCameraButton = document.getElementById('close-camera');
        const capturePhotoButton = document.getElementById('capture-photo');
        const videoElement = document.getElementById('camera-video');
        const form = document.getElementById('avp-form');
        const locationStatus = document.getElementById('location-status');
        
        let photoCount = 0;
        let userLocation = null;
        let stream = null;
        
        // Fonction pour ajouter une photo à partir de données d'image
        function addPhotoFromData(imageData) {
            photoCount++;
            const photoItem = document.createElement('div');
            photoItem.className = 'photo-item';
            photoItem.innerHTML = `
                <div class="photo-preview">
                    <img src="${imageData}" alt="Photo ${photoCount}">
                </div>
                <div class="photo-controls">
                    <p>Photo ${photoCount} - Prise avec l'appareil photo</p>
                    <input type="hidden" name="photos[]" value="${imageData}">
                    <button type="button" class="btn btn-danger remove-photo">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            `;
            photosContainer.appendChild(photoItem);
            
            // Gestionnaire pour supprimer le champ photo
            const removeButton = photoItem.querySelector('.remove-photo');
            removeButton.addEventListener('click', function() {
                if (photosContainer.children.length > 1) {
                    photosContainer.removeChild(photoItem);
                } else {
                    alert('Vous devez avoir au moins une photo.');
                }
            });
        }
        
        // Ouvrir l'appareil photo
        openCameraButton.addEventListener('click', async function() {
            try {
                cameraModal.style.display = 'flex';
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'environment' }, 
                    audio: false 
                });
                videoElement.srcObject = stream;
            } catch (error) {
                console.error("Erreur d'accès à la caméra: ", error);
                alert("Impossible d'accéder à l'appareil photo: " + error.message);
                cameraModal.style.display = 'none';
            }
        });
        
        // Fermer l'appareil photo
        closeCameraButton.addEventListener('click', function() {
            cameraModal.style.display = 'none';
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        });
        
        // Capturer la photo
        capturePhotoButton.addEventListener('click', function() {
            const canvas = document.createElement('canvas');
            canvas.width = videoElement.videoWidth;
            canvas.height = videoElement.videoHeight;
            const context = canvas.getContext('2d');
            context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
            
           
            const imageData = canvas.toDataURL('image/jpeg');
            addPhotoFromData(imageData);
            
            
            cameraModal.style.display = 'none';
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        });
        
        
        function getLocation() {
            if (navigator.geolocation) {
                locationStatus.textContent = "Localisation en cours...";
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        userLocation = {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            altitude: position.coords.altitude || null,
                            accuracy: position.coords.accuracy || null
                        };
                        
                        
                        document.getElementById('latitude-input').value = userLocation.latitude;
                        document.getElementById('longitude-input').value = userLocation.longitude;
                        document.getElementById('altitude-input').value = userLocation.altitude;
                        document.getElementById('precision-input').value = userLocation.accuracy;
                        
                        locationStatus.innerHTML = `
                            <span><i class="fas fa-check-circle"></i> Localisation obtenue avec succès!</span><br>
                            Latitude: <span>${userLocation.latitude.toFixed(6)}</span><br>
                            Longitude: <span>${userLocation.longitude.toFixed(6)}</span><br>
                            ${userLocation.altitude ? `Altitude: <span>${userLocation.altitude.toFixed(2)} mètres</span><br>` : ''}
                            Précision: <span>${userLocation.accuracy ? userLocation.accuracy.toFixed(2) + ' mètres' : 'Non disponible'}</span>
                        `;
                    },
                    function(error) {
                        console.error("Erreur de géolocalisation: ", error);
                        locationStatus.innerHTML = `<span style="color: #ef4444;"><i class="fas fa-exclamation-triangle"></i> Impossible d'obtenir la localisation: ${error.message}</span>`;
                        
                      
                        document.getElementById('latitude-input').value = '';
                        document.getElementById('longitude-input').value = '';
                        document.getElementById('altitude-input').value = '';
                        document.getElementById('precision-input').value = '';
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                locationStatus.innerHTML = `<span style="color: #ef4444;"><i class="fas fa-exclamation-triangle"></i> La géolocalisation n'est pas supportée par votre navigateur</span>`;
            }
        }
        
       
        getLocation();
        
       
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            
            const commune = document.getElementById('commune').value;
            const lieu = document.getElementById('lieu').value;
            
            if (!commune || !lieu) {
                alert('Veuillez remplir tous les champs obligatoires.');
                return;
            }
            
            if (!userLocation) {
                if (!confirm("La localisation n'a pas pu être obtenue. Souhaitez-vous tout de même soumettre le formulaire?")) {
                    return;
                }
            }
            
           
            Swal.fire({
                title: 'Confirmation',
                text: "Êtes-vous sûr de vouloir envoyer ce rapport d'accident?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FF7F00',
                cancelButtonColor: '#003366',
                confirmButtonText: 'Oui, envoyer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                  
                    form.submit();
                }
            });
        });
    });
</script>
@endsection