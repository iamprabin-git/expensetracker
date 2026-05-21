import Swiper from 'swiper';
import { Autoplay, Navigation, Pagination } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

export function initReviewsCarousel() {
    const root = document.querySelector('[data-reviews-swiper]');

    if (!root) {
        return;
    }

    const slideCount = root.querySelectorAll('.swiper-slide').length;

    if (slideCount === 0) {
        return;
    }

    new Swiper(root, {
        modules: [Navigation, Pagination, Autoplay],
        slidesPerView: 1,
        spaceBetween: 20,
        loop: slideCount > 1,
        speed: 500,
        autoplay:
            slideCount > 1
                ? {
                      delay: 5500,
                      disableOnInteraction: false,
                      pauseOnMouseEnter: true,
                  }
                : false,
        pagination: {
            el: root.querySelector('.reviews-swiper-pagination'),
            clickable: true,
            dynamicBullets: slideCount > 4,
        },
        navigation: {
            nextEl: root.querySelector('.reviews-swiper-next'),
            prevEl: root.querySelector('.reviews-swiper-prev'),
        },
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initReviewsCarousel);
} else {
    initReviewsCarousel();
}
