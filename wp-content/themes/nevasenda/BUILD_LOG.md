# Nevasenda — log de construcción

Sitio WP local: `Local Sites/senderismo/app/public`. Tema custom clásico (sin child theme), paleta blanco/negro/azul.

## Paso 1 — Estructura tema
- `style.css`: cabecera tema + variables CSS (`--c-white #fff`, `--c-black #181818`, `--c-blue #1565c0`, `--c-blue-dark #0d47a1`), reset, header/nav (sticky + responsive con toggle móvil), hero oscuro, grid de tarjetas, badges, footer negro.
- `functions.php`: setup tema (title-tag, post-thumbnails, html5), registro menús `primary`/`footer`, enqueue de `style.css` + `assets/nav.js`.

## Paso 2 — CPT Rutas
- CPT `ruta` (slug `/rutas/`), público, con archivo, soporta título/editor/imagen destacada/extracto, `show_in_rest` true (compatible editor bloques).
- Taxonomías `dificultad` y `zona` (jerárquicas, slugs `/dificultad/` y `/zona/`).
- Metabox "Datos técnicos" en editor de Ruta: distancia (km), desnivel (m), duración. Guardado en postmeta `_ruta_distancia`, `_ruta_desnivel`, `_ruta_duracion` (nonce + sanitización).
- Helper `nevasenda_ruta_meta( $post_id, $key )` pa leer esos datos en plantillas.

## Paso 3 — Plantillas
- `header.php` / `footer.php`: header sticky con logo "Neva**senda**", nav principal + toggle móvil; footer negro con menú footer + copyright.
- `front-page.php`: hero + sección "Rutas destacadas" (3 últimas, con badges distancia/dificultad) + sección "Últimas noticias" (3 últimos posts).
- `home.php`: listado blog (grid de tarjetas + paginación).
- `page.php`: página genérica (título + imagen destacada opcional + contenido). Sirve también pa "Sobre nosotros" y "Contacto".
- `single.php`: entrada de blog + comentarios.
- `single-ruta.php`: ficha de ruta — badges zona/dificultad, imagen, bloque "Datos técnicos" (distancia/desnivel/duración), contenido.
- `archive-ruta.php`: listado `/rutas/` en grid con badges + paginación.
- `index.php`: fallback genérico.

## Paso 4 — Rediseño moderno (inspirado Awwwards: tipografía bold, gradientes con blur, micro-interacciones, scroll-reveal)
- Tipografía: `Space Grotesk` (títulos, bold/geométrica) + `Inter` (cuerpo), vía Google Fonts en `functions.php`. Tamaños con `clamp()`.
- Header: sticky con transición a `backdrop-filter: blur` + sombra al hacer scroll (clase `.is-scrolled` añadida por JS).
- Nav: subrayado animado (`::after` width 0→100%) en hover/activo.
- Hero: blobs radiales azules con `filter: blur()` y animación `blob-float` infinita; título/párrafo/botón entran con `fade-up` escalonado.
- Botones: hover con `translateY` + sombra azul (`--shadow-blue`).
- Tarjetas: hover lift (`translateY(-8px)` + sombra) y zoom de imagen (`scale(1.08)`).
- Scroll-reveal: `assets/animations.js` añade clase `.reveal` + `IntersectionObserver` a tarjetas, títulos de sección y datos de ruta, con stagger vía `--reveal-i`.
- `prefers-reduced-motion: reduce` desactiva todas las animaciones.

## Paso 5 — Contenido demo (estructura tipo nevasport, adaptado a senderismo)
- `wp-content/mu-plugins/nevasenda-demo-content.php`: importador "one-shot" (no wp-cli disponible).
- Crea taxonomías: Dificultad (Fácil/Media/Difícil), Zona (Picos de Europa, Sierra Nevada, Pirineos, Sierra de Gredos, Sierra de Francia).
- Crea 5 Rutas demo (Cares, Órganos, Circo de Gredos, Veleta, Néouvielle) con datos técnicos completos.
- Crea 3 entradas de blog estilo "noticias" (nieve tardía, señalización senderos, consejos equipo verano) — mismo formato editorial que nevasport pero contenido propio de senderismo.
- **Ejecutar una vez**: logueado como admin, visitar `/wp-admin/?nevasenda_import=1`. Marca opción `nevasenda_demo_imported` pa no duplicar.

## Paso 6 — Fotos reales, nav fix, parallax y galería
- **Nav arreglado**: `wp_nav_menu` ahora usa `fallback_cb` → `nevasenda_fallback_menu()` (functions.php). Si no hay menú asignado en Apariencia > Menús, muestra Inicio/Rutas/Blog automáticamente (antes no pintaba nada y el nav "no se veía").
- **Imágenes** descargadas de Unsplash (libres de uso) a `assets/images/`: `hero-bg.jpg`, `section-1.jpg`, `section-2.jpg`, `gallery-1..8.jpg`. Fotos de rutas a `wp-content/uploads/2026/06/ruta-*.jpg`.
- **Hero**: foto de fondo de montaña + degradado oscuro/azul (`.hero-bg-img` + `::before` gradiente) + parallax suave.
- **Photo sections** (`.photo-section--left/--right`): foto a pantalla completa + degradado lateral + párrafo + botón. 2 añadidas en home ("Más que rutas, experiencias" / "Comparte tu aventura").
- **Galería**: grid tipo mosaico (`.gallery-grid`, 1 grande + 7 normales) con hover zoom + overlay, y lightbox JS (clic = ampliar, Esc/clic fuera = cerrar). Sección "Galería" en home + página completa `page-galeria.php` (crear página con slug `galeria`).
- **Parallax**: `assets/animations.js` mueve `.hero-bg-img` / `.photo-section-bg` según scroll (`translateY`, factor .15), respeta `prefers-reduced-motion`.
- **Importador demo actualizado**: ahora también sube imagen destacada por ruta (Cares, Órganos, Gredos, Veleta, Néouvielle) desde `uploads/2026/06/`.
- **Reimportar con fotos**: si ya corriste `?nevasenda_import=1` antes, visita `?nevasenda_reset=1` (borra el contenido demo) y luego `?nevasenda_import=1` otra vez.

## Paso 7 — Rediseño v2 (impacto visual, basado en investigación Awwwards/Komoot/AllTrails/Primland)
- Nuevo acento `--c-amber: #f0a93c` en `:root` (azul/negro/blanco siguen dominando, ámbar solo pa highlights).
- **Hero**: banda `.hero-stats` con contadores animados (rutas publicadas y km mapeados calculados de verdad desde los CPT `ruta`, comunidad/zonas como datos demo) + flecha `.hero-scroll` con animación de scroll-cue.
- **Marquee**: franja negra con nombres de zonas en bucle infinito (`@keyframes marquee`, CSS puro, pausa en hover, se desactiva con `prefers-reduced-motion`).
- **Tarjetas de ruta**: `.card-overlay` que aparece sobre la imagen (hover/focus o siempre en táctil vía `@media (hover:none)`) mostrando desnivel y duración.
- **Scrollytelling**: nueva sección con imagen sticky + 4 bloques de texto que se resaltan según scroll (`IntersectionObserver`, clase `.is-active`), pensada pa explicar "por qué Nevasenda". En móvil se apila sin sticky.
- **Comunidad y noticias**: sección combinada con 2 tarjetas de blog + bloque `.forum-preview` (3 hilos de ejemplo con avatar de inicial) como adelanto visual de un futuro foro.
- **Galería**: ampliada de 8 a 14 fotos (`gallery-9..14.jpg` nuevas de Unsplash) con `grid-auto-flow: dense` y botón "Ver galería completa" hacia `/galeria/`.
- **Footer**: bloque `.footer-social` con iconos SVG inline (Instagram/Facebook/YouTube, enlaces placeholder `#`).
- `assets/animations.js`: añadido contador animado (`[data-counter]`, ease-out con `requestAnimationFrame`) y observer de scrollytelling; ambos respetan `prefers-reduced-motion`.

## Paso 8 — Ajustes nav transparente + nueva foto hero
- **Nav portada**: fondo negro sólido (`var(--c-black)`) al hacer scroll en vez de blanco difuminado; texto/branding/toggle blancos tanto sobre el hero como con fondo negro. Menú móvil (`.primary-menu.is-open`) también pasa a negro en portada.
- Quitado el subrayado `::after` del nav en portada (causaba líneas blancas raras bajo "Inicio" y "Comunidad" porque ambos quedaban marcados como `current-menu-item`).
- **Hero**: nueva foto `hero-bg.jpg` (2560x1504, picos nevados con alpenglow al amanecer, Unsplash).

## Pendiente (próximos pasos)
- Activar tema "Nevasenda" en Apariencia.
- Crear menú "Menú principal" en Apariencia > Menús y asignarlo a `primary` (Inicio, Rutas, Blog, Material, Galería, Sobre nosotros, Contacto). Ajustes > Lectura: página de entradas = "Blog".
- Crear páginas: Sobre nosotros, Contacto (form con plugin, ej. Contact Form 7), Material/Galería.
- Crear términos de taxonomía: Dificultad (Fácil/Media/Difícil), Zona (por comarca/región).
- Añadir contenido de prueba (2-3 rutas, 2-3 posts) pa ver diseño con datos reales.
- Logo real (de momento texto "Nevasenda").
