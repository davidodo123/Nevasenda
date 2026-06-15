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
- **Hero**: nueva foto `hero-bg.jpg` (2560x1705, vista de Sierra Nevada nevada desde la Alhambra, Granada — Unsplash).

## Paso 9 — Foro real (Asgaros Forum)
- Instalado y activado el plugin **Asgaros Forum** (GPL, `wp-content/plugins/asgaros-forum`) vía `nevasenda-activar-foro.php` (one-shot).
- Creada categoría "Comunidad Nevasenda" con 3 foros desde Apariencia > Asgaros Forum (admin): **Rutas y estado de senderos**, **Equipo y material**, **General y quedadas**.
- `page-foro.php` ahora renderiza `the_content()` dentro de `.forum-wrapper` — la página "Foro" debe tener como contenido el shortcode `[asgarosforum]` (se asigna vía `nevasenda-foro-shortcode.php`, one-shot: visitar `/wp-admin/?nevasenda_foro_shortcode=1` logueado como admin).
- `front-page.php`: el bloque `.forum-preview` ya no usa hilos de ejemplo hardcodeados — calcula `$foros_preview` recorriendo `$asgarosforum->content->get_categories()` / `get_forums()` y enlaza a cada foro real con `get_link('forum', $id)`, mostrando nº de temas (`get_forum_topic_counter()`) y descripción.
- `style.css`: nueva sección "Foro real (Asgaros Forum)" — `.forum-wrapper` (tarjeta a juego con el resto del sitio) + overrides de `#af-wrapper` (tipografía `--font-base`, azul `--c-blue`/`--c-blue-dark` en vez del `#256db3` por defecto, filas alternas en `--c-gray-100`, inputs/botones con el radio y bordes del tema).

## Paso 10 — Blog: contenido propio, página dedicada y mejor diseño de tarjetas
- `wp-content/mu-plugins/nevasenda-blog-content.php`: importador one-shot con 5 entradas de blog propias (refugios Pirineos, guía de botas, crónica Picos de Europa, estado senderos Sierra Nevada, quedadas de otoño). Visitar `/wp-admin/?nevasenda_blog_import=1` logueado como admin (`?nevasenda_blog_reset=1` pa borrar y reimportar).
- `wp-content/mu-plugins/nevasenda-fix-blog-page.php`: crea página "Blog", la fija como página de entradas (Ajustes > Lectura) y actualiza el enlace del menú. Visitar `/wp-admin/?nevasenda_fix_blog=1`.
- **Fix "Inicio" llevaba al Blog**: la versión anterior de `nevasenda-fix-blog-page.php` ponía `show_on_front = page` y `page_for_posts` = página Blog, pero no fijaba `page_on_front`, así que `/` también acababa mostrando `home.php` (listado de posts) en vez de `front-page.php`. Ahora el script también crea una página vacía "Inicio" (`/inicio/`, solo de soporte — el contenido real de `/` lo sigue pintando `front-page.php`, que tiene prioridad sobre `page.php` pa la portada) y la asigna como `page_on_front`. **Hay que volver a visitar `/wp-admin/?nevasenda_fix_blog=1` logueado como admin** pa aplicar el fix (es idempotente, no duplica páginas).
- `wp-content/mu-plugins/nevasenda-menu-add-foro.php`: añade el ítem "Comunidad"/Foro al menú principal (one-shot).
- **Fotos**: cada entrada de blog ahora tiene imagen destacada propia (`uploads/2026/06/blog-refugios.jpg`, `blog-botas.jpg`, `blog-picos-europa.jpg`, `blog-sierra-nevada.jpg`, `blog-quedadas.jpg`, de Unsplash). El importador las asigna al crear las entradas. Pa entradas ya importadas antes sin foto, visitar `/wp-admin/?nevasenda_blog_images=1` (one-shot, no duplica si ya tienen imagen destacada).
- **Tarjetas de blog rediseñadas** (`home.php` + `style.css`): la primera entrada del listado (`.cards-grid--blog .card--featured`) ocupa el ancho completo en formato 2 columnas (foto grande + texto), el resto sigue en grid normal. Todas las tarjetas con foto muestran un badge `.card-date` (día/mes) sobre la imagen, y un `.card-meta-row` con fecha completa + minutos de lectura estimados (`nevasenda_reading_time()` en `functions.php`, ~200 palabras/min).
- `single.php`: añadido `.entry-meta` (fecha + minutos de lectura) bajo el título de cada entrada, mismo estilo que las tarjetas.

## Paso 11 — Tarjetas de ruta rediseñadas (AllTrails/Komoot)
- Nueva clase `.ruta-card` (sustituye a `.card` en "Rutas destacadas" del home y en `/rutas/`), grid `.rutas-grid`. Foto a pantalla completa con degradado inferior; sobre la imagen, pills flotantes con dificultad (color por nivel: Fácil azul, Media ámbar, Difícil negro) y distancia, más título y zona (icono pin) en overlay inferior.
- Debajo de la foto, fila de estadísticas con iconos (`.ruta-card__stats`): distancia, desnivel y duración, separadas por divisores.
- Pie de tarjeta `.ruta-card__link` "Ver ruta" con flecha animada al hover.
- Iconos SVG inline centralizados en `nevasenda_icon( $name )` (`functions.php`): `ruler`, `terrain`, `clock`, `pin`, `arrow`.
- Quitada la clase `.card-overlay` (ya no se usa, era de las tarjetas de ruta antiguas). `.badge`/`.badge-blue`/`.card-meta` se mantienen porque `single-ruta.php` los sigue usando pa los badges de zona/dificultad de la ficha.

## Paso 12 — Ficha de ruta: mapa Wikiloc y requisitos/material
- Metabox "Datos técnicos" (editor de Ruta) ampliado con 2 campos nuevos: **Enlace a Wikiloc** (URL del track) y **Requisitos / material recomendado** (textarea, un punto por línea). Guardado en `_ruta_wikiloc` y `_ruta_requisitos`.
- `single-ruta.php`: nueva sección `.ruta-extra` (2 columnas, debajo del contenido):
  - **Mapa de la ruta**: si la URL de Wikiloc trae un ID numérico (`nevasenda_wikiloc_id()` en `functions.php`, extrae `/\d{5,}/` de la URL), embebe el widget `https://es.wikiloc.com/wikiloc/spatialArtifactWidget.do?id=...` (mapa + perfil del track) + botón "Ver track completo en Wikiloc" (abre en pestaña nueva). Si no hay ID pero sí URL, solo se muestra el botón.
  - **Requisitos y material recomendado**: cada línea del campo se pinta como item de checklist con icono `check`.
  - Toda la sección se oculta si la ruta no tiene ni Wikiloc ni requisitos rellenados.
- Pa probarlo: editar una Ruta existente, rellenar "Enlace a Wikiloc" (ej. `https://es.wikiloc.com/rutas-senderismo/xxxxx-123456789`) y/o "Requisitos / material recomendado", guardar.

## Paso 13 — Wikiloc sin iframe (CSP), datos prácticos y botón de relleno aleatorio
- **Wikiloc no se puede embeber**: comprobado con curl que `spatialArtifactWidget.do` envía `Content-Security-Policy: frame-ancestors 'self' https://*.wikiloc.com;` — bloqueo del lado de Wikiloc, ningún sitio externo puede meterlo en un iframe. Se quita el intento de iframe (y el helper `nevasenda_wikiloc_id()`).
- **`.ruta-map-cta`**: la sección "Mapa de la ruta" ahora es una tarjeta con degradado azul, icono de pin grande, texto y botón `btn-outline` "Ver track completo en Wikiloc" (abre en pestaña nueva).
- **Datos técnicos (`_ruta_punto_encuentro`, `_ruta_hora_salida`)**: 2 campos nuevos en el metabox "Datos técnicos". `.ruta-datos` vuelve a ser solo distancia/desnivel/duración (3 columnas limpias); punto de encuentro y hora de salida se muestran debajo en una barra `.ruta-meeting` (icono pin / icono reloj + texto), no como "dato" numérico grande.
- **Botón "Rellenar con datos de ejemplo"**: en el metabox, debajo de Requisitos. JS inline rellena punto de encuentro, hora de salida y 4-6 requisitos aleatorios desde listas predefinidas (no guarda solo, hay que pulsar Actualizar).
- Pa probarlo: editar una Ruta, pulsar "Rellenar con datos de ejemplo" (o rellenar a mano), guardar, ver ficha.

## Paso 14 — Mapa real con track GPX (Leaflet)
- Nuevo campo en metabox "Datos técnicos": **Track GPX** — botón "Seleccionar GPX" abre el selector de medios de WP (`wp.media`), guarda la URL del archivo en `_ruta_gpx`. Filtro `upload_mimes` añade `.gpx` (`application/gpx+xml`) a los tipos permitidos en la mediateca.
- `single-ruta.php`: si la ruta tiene GPX, "Mapa de la ruta" muestra un mapa **Leaflet + OpenStreetMap real** (`#ruta-leaflet-map`, 420px), con el track dibujado y el mapa centrado/ajustado automáticamente (`fitBounds`). Debajo, botón "Ver ruta en Wikiloc" si también hay enlace.
- Si no hay GPX pero sí Wikiloc: se mantiene la tarjeta `.ruta-map-cta` (degradado azul + botón) del Paso 13.
- Librerías cargadas solo en fichas de ruta con GPX (`is_singular('ruta')` + meta `_ruta_gpx`), vía CDN: `leaflet@1.9.4` + `leaflet-gpx@2.1.2`. Sin API key, sin coste.
- Pa probarlo: descargar el `.gpx` de la ruta desde Wikiloc (botón "Descargar" en la página de la ruta, requiere cuenta), editar la Ruta en wp-admin, "Seleccionar GPX" → subir el archivo, guardar, recargar ficha.
- **Fix**: `.gpx` se rechazaba al subir aunque `upload_mimes` lo permitiera, porque `wp_check_filetype_and_ext()` detecta el tipo real (finfo) como `application/xml`, no `application/gpx+xml`. Filtro `nevasenda_fix_gpx_filetype()` en `wp_check_filetype_and_ext` fuerza `ext=gpx`/`type=application/gpx+xml` cuando la extensión es `.gpx` y WP no reconoce el tipo.

## Paso 15 — Etapas múltiples en el mapa
- El campo único "Track GPX" se sustituye por un repetidor **"Etapas de la ruta"** en el metabox: cada etapa tiene un nombre (ej. "Etapa 1: Refugio - Cumbre") y su propio `.gpx`, botones "+ Añadir etapa" / "Eliminar etapa". Se guarda como array serializado en `_ruta_etapas` (helper `nevasenda_ruta_etapas()`).
- Migración automática: si una ruta tenía el antiguo `_ruta_gpx` (un solo track con extensión `.gpx`), se carga como "Etapa 1" al abrir el editor.
- `single-ruta.php`: el mapa Leaflet dibuja **todas las etapas a la vez**, cada una con un color distinto (`nevasenda_etapa_colors()`), y ajusta la vista (`fitBounds`) a la unión de todos los tracks. Si hay más de una etapa, se muestra una leyenda con el nombre y color de cada una debajo del mapa.
- Pa probarlo: editar una Ruta, "+ Añadir etapa" varias veces, dar nombre y subir un `.gpx` distinto a cada una, guardar, recargar ficha — el mapa debe mostrar todos los tracks con colores diferentes y la leyenda debajo.
- **Fix marcadores**: los `<wpt>` (puntos de interés del GPX: lagos, refugios, miradores...) salían como icono roto, porque `leaflet-gpx` usa por defecto rutas relativas a iconos que no existen. Se fijan `marker_options` en el script inline apuntando a los iconos por defecto de Leaflet (`marker-icon.png` / `marker-shadow.png`), con tamaños/anclas correctos — ahora salen como chinchetas azules con popup (nombre del punto).

## Paso 16 — Sección "Rutas para todos" rediseñada + galería gestionable desde wp-admin
- Front-page: la sección "scrollytelling" (bloques que se resaltaban al hacer scroll) se sustituye por una rejilla estática `.features-grid` (imagen + 4 `.feature-card` con icono/título/texto: niveles, datos técnicos, comunidad, fotografía). Se quita el JS del IntersectionObserver de scrolly en `animations.js`.
- Nuevo CPT **`foto_galeria`** ("Galería" en el menú admin): título + imagen destacada + orden (atributo de página). Helper `nevasenda_galeria_fotos( $limit )` devuelve las fotos publicadas con miniatura, ordenadas por "Orden".
- `front-page.php` y `page-galeria.php`: la rejilla `.gallery-grid` ya no recorre `gallery-N.jpg` fijas del tema, recorre `nevasenda_galeria_fotos()`.
- Sección "Comparte tu aventura" (CTA antes de la galería) ya no habla de subir fotos propias — ahora "Inspírate pa tu próxima ruta", solo enlaza a `/galeria/`.
- mu-plugin **`nevasenda-galeria-import.php`**: importación única de las 14 fotos `gallery-N.jpg` del tema como posts `foto_galeria` con imagen destacada. Visitar `/wp-admin/?nevasenda_galeria_import=1` (logueado admin) una vez; `?nevasenda_galeria_reset=1` borra lo importado pa reimportar.
- A partir de ahora, fotos de galería se gestionan en wp-admin → **Galería** → Añadir nueva foto (título + imagen destacada; "Orden" controla la posición).

## Hecho (ya no pendiente)
- ✅ Tema activado, menú principal asignado a `primary` (Inicio, Rutas, Galería, Comunidad, Blog) vía `wp-content/mu-plugins/nevasenda-menu-fix.php` (one-shot, se puede borrar ya que cumplió su función).
- ✅ Taxonomías y contenido demo importados (`nevasenda-demo-content.php`).
- ✅ Galería con 14 fotos + página `/galeria/`.
- ✅ Foro real con Asgaros Forum, 3 foros creados, integrado en `/foro/` y preview dinámico en home.
- ✅ Blog con contenido propio, página dedicada y tarjetas con foto + fecha + tiempo de lectura.

## Pendiente (próximos pasos)
- Crear páginas: Sobre nosotros, Contacto (form con plugin, ej. Contact Form 7).
- Logo real (de momento texto "Nevasenda").
- Borrar `wp-content/mu-plugins/nevasenda-menu-fix.php` una vez confirmado el menú en Apariencia > Menús.
- Visitar `/wp-admin/?nevasenda_foro_shortcode=1` (si aún no se ha hecho) pa que `/foro/` muestre el foro real.
- Ejecutar (si no se ha hecho) `?nevasenda_blog_import=1`, `?nevasenda_fix_blog=1` y `?nevasenda_blog_images=1` pa que el blog tenga contenido, página dedicada y fotos.
