document.addEventListener( 'DOMContentLoaded', function () {

	// Header: estado "scrolled" pa blur/sombra
	var header = document.querySelector( '.site-header' );
	if ( header ) {
		var onScroll = function () {
			header.classList.toggle( 'is-scrolled', window.scrollY > 10 );
		};
		onScroll();
		window.addEventListener( 'scroll', onScroll, { passive: true } );
	}

	// Scroll-reveal con stagger automático
	var targets = document.querySelectorAll(
		'.cards-grid .card, .news-cards .card, .forum-preview, .section-title, .section-subtitle, .ruta-datos .dato'
	);

	if ( ! targets.length ) {
		return;
	}

	if ( ! ( 'IntersectionObserver' in window ) ) {
		targets.forEach( function ( el ) { el.classList.add( 'is-visible' ); } );
		return;
	}

	targets.forEach( function ( el, i ) {
		el.classList.add( 'reveal' );
		el.style.setProperty( '--reveal-i', i % 6 );
	} );

	var observer = new IntersectionObserver( function ( entries, obs ) {
		entries.forEach( function ( entry ) {
			if ( entry.isIntersecting ) {
				entry.target.classList.add( 'is-visible' );
				obs.unobserve( entry.target );
			}
		} );
	}, { threshold: 0.15 } );

	targets.forEach( function ( el ) { observer.observe( el ); } );

	// Parallax suave en fotos de fondo (hero + secciones)
	var parallaxEls = document.querySelectorAll( '.hero-bg-img, .photo-section-bg' );
	if ( parallaxEls.length ) {
		var ticking = false;
		var updateParallax = function () {
			parallaxEls.forEach( function ( el ) {
				var rect = el.parentElement.getBoundingClientRect();
				el.style.transform = 'translateY(' + ( rect.top * 0.15 ) + 'px)';
			} );
			ticking = false;
		};
		var onScroll = function () {
			if ( ! ticking ) {
				requestAnimationFrame( updateParallax );
				ticking = true;
			}
		};
		updateParallax();
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		window.addEventListener( 'resize', updateParallax );
	}

	// Lightbox pa la galería
	var galleryLinks = document.querySelectorAll( '.gallery-grid a' );
	if ( galleryLinks.length ) {
		var lightbox = document.createElement( 'div' );
		lightbox.className = 'lightbox';
		lightbox.innerHTML = '<button class="lightbox-close" aria-label="Cerrar">&times;</button><img alt="">';
		document.body.appendChild( lightbox );
		var lbImg = lightbox.querySelector( 'img' );

		var closeLightbox = function () { lightbox.classList.remove( 'is-open' ); };

		galleryLinks.forEach( function ( link ) {
			link.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				var img = link.querySelector( 'img' );
				lbImg.src = link.getAttribute( 'href' );
				lbImg.alt = img ? img.alt : '';
				lightbox.classList.add( 'is-open' );
			} );
		} );

		lightbox.addEventListener( 'click', function ( e ) {
			if ( e.target === lightbox || e.target.classList.contains( 'lightbox-close' ) ) {
				closeLightbox();
			}
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' ) closeLightbox();
		} );
	}

	// Contadores animados del hero
	var reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var counters = document.querySelectorAll( '[data-counter]' );
	if ( counters.length ) {
		if ( reduceMotion || ! ( 'IntersectionObserver' in window ) ) {
			counters.forEach( function ( el ) {
				el.textContent = el.getAttribute( 'data-target' );
			} );
		} else {
			var animateCounter = function ( el ) {
				var target   = parseInt( el.getAttribute( 'data-target' ), 10 ) || 0;
				var duration = 1500;
				var start    = null;

				var step = function ( timestamp ) {
					if ( start === null ) start = timestamp;
					var progress = Math.min( ( timestamp - start ) / duration, 1 );
					var eased    = 1 - Math.pow( 1 - progress, 3 );
					el.textContent = Math.round( eased * target );
					if ( progress < 1 ) {
						requestAnimationFrame( step );
					}
				};
				requestAnimationFrame( step );
			};

			var counterObserver = new IntersectionObserver( function ( entries, obs ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						animateCounter( entry.target );
						obs.unobserve( entry.target );
					}
				} );
			}, { threshold: 0.4 } );

			counters.forEach( function ( el ) { counterObserver.observe( el ); } );
		}
	}

	// Scrollytelling: resalta el bloque activo según el scroll
	var scrollyItems = document.querySelectorAll( '.scrolly-item' );
	if ( scrollyItems.length ) {
		if ( ! ( 'IntersectionObserver' in window ) ) {
			scrollyItems.forEach( function ( el ) { el.classList.add( 'is-active' ); } );
		} else {
			scrollyItems[ 0 ].classList.add( 'is-active' );

			var scrollyObserver = new IntersectionObserver( function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						scrollyItems.forEach( function ( el ) { el.classList.remove( 'is-active' ); } );
						entry.target.classList.add( 'is-active' );
					}
				} );
			}, { threshold: 0.6 } );

			scrollyItems.forEach( function ( el ) { scrollyObserver.observe( el ); } );
		}
	}
} );
