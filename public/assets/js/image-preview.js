document.addEventListener('DOMContentLoaded', function() {
    function createImagePreviewModal(modalId) {
        const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Preview Gambar</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="imageCarousel_${modalId}" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner"></div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel_${modalId}" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel_${modalId}" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            </div>
                            <div class="thumbnail-container d-flex justify-content-center mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const existingModal = document.getElementById(modalId);
        if (existingModal) {
            existingModal.remove();
        }
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        return document.getElementById(modalId);
    }

    function populateImageModal(modal, images, baseUrl, uploadPath) {
        const carouselInner = modal.querySelector('.carousel-inner');
        const thumbnailContainer = modal.querySelector('.thumbnail-container');
        
        carouselInner.innerHTML = '';
        thumbnailContainer.innerHTML = '';

        images.forEach((image, index) => {
            const carouselItem = document.createElement('div');
            carouselItem.className = `carousel-item ${index === 0 ? 'active' : ''}`;
            carouselItem.innerHTML = `
                <img src="${baseUrl}/${uploadPath}/${image}" 
                     class="d-block w-100" 
                     style="height: 400px; object-fit: contain">
            `;
            carouselInner.appendChild(carouselItem);

            const thumbnail = document.createElement('img');
            thumbnail.src = `${baseUrl}/${uploadPath}/${image}`;
            thumbnail.className = `thumbnail ${index === 0 ? 'active' : ''}`;
            thumbnail.style.cssText = 'width: 60px; height: 60px; object-fit: cover; cursor: pointer; border: 2px solid transparent;';
            
            thumbnail.onclick = () => {
                const carouselElement = modal.querySelector('.carousel');
                const carousel = bootstrap.Carousel.getInstance(carouselElement) || new bootstrap.Carousel(carouselElement);
                carousel.to(index);
                thumbnailContainer.querySelectorAll('.thumbnail').forEach(thumb => thumb.classList.remove('active'));
                thumbnail.classList.add('active');
            };
            
            thumbnailContainer.appendChild(thumbnail);
        });

        const carousel = modal.querySelector('.carousel');
        carousel.addEventListener('slid.bs.carousel', (event) => {
            const activeIndex = event.to;
            thumbnailContainer.querySelectorAll('.thumbnail').forEach((thumb, index) => {
                thumb.classList.toggle('active', index === activeIndex);
            });
        });
    }

    document.querySelectorAll('.image-preview-trigger[data-images]').forEach(img => {
        img.addEventListener('click', function() {
            try {
                const images = JSON.parse(this.dataset.images);
                if (!Array.isArray(images)) throw new Error('Invalid image data');

                const modal = createImagePreviewModal('vehiclePreviewModal');
                modal.querySelector('.modal-title').textContent = 'Foto Kendaraan';
                populateImageModal(modal, images, BASE_URL, 'uploads/images');
                
                const modalInstance = new bootstrap.Modal(modal);
                modalInstance.show();
            } catch (error) {
                console.error('Error showing vehicle preview:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal menampilkan preview gambar kendaraan'
                });
            }
        });
    });

    document.querySelectorAll('.image-preview-trigger[data-ruangan]').forEach(img => {
        img.addEventListener('click', function() {
            try {
                const ruangan = JSON.parse(this.dataset.ruangan);
                const fotos = JSON.parse(this.dataset.fotos);
                if (!ruangan || !Array.isArray(fotos)) throw new Error('Invalid room data');

                const modal = createImagePreviewModal('roomPreviewModal');
                modal.querySelector('.modal-title').textContent = 'Foto Ruangan';
                populateImageModal(modal, fotos, baseUrl, 'uploads/ruangan');
                
                const modalInstance = new bootstrap.Modal(modal);
                modalInstance.show();
            } catch (error) {
                console.error('Error showing room preview:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal menampilkan preview gambar ruangan'
                });
            }
        });
    });
});