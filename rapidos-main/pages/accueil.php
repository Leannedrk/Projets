<div class="main">
    <!-- Corps de la page présentation -->
    <div class="slider-container">
        <!-- Menu pour les dots du slider -->
        <div class="menu">
            <label for="slide-dot-1"></label> <!-- Dot pour la première slide -->
            <label for="slide-dot-2"></label> <!-- Dot pour la deuxième slide -->
            <label for="slide-dot-3"></label> <!-- Dot pour la troisième slide -->
        </div>

        <!-- Boutons pour naviguer entre les slides -->
        <button class="carousel-button left" onclick="prevImage()">&#10094;</button> <!-- Bouton pour aller à l'image précédente -->
        <button class="carousel-button right" onclick="nextImage()">&#10095;</button> <!-- Bouton pour aller à l'image suivante -->

        <!-- Définition des dots et des slides associées -->
        <input id="slide-dot-1" type="radio" name="slides" checked> <!-- Dot activée par défaut -->
        <div class="slide slide-1"></div> <!-- Première slide -->
        <input id="slide-dot-2" type="radio" name="slides">
        <div class="slide slide-2"></div> <!-- Deuxième slide -->
        <input id="slide-dot-3" type="radio" name="slides">
        <div class="slide slide-3"></div> <!-- Troisième slide -->
    </div>

    <script>
        // Index actuel de l'image affichée
        let currentIndex = 0;

        // Fonction pour afficher une image spécifique en fonction de l'index
        function showImage(index) {
            const images = document.querySelectorAll('.carousel-images img'); // Sélection de toutes les images du carrousel
            const totalImages = images.length; // Nombre total d'images

            // Gestion de l'index pour éviter les dépassements
            if (index >= totalImages) {
                currentIndex = 0; // Retour au début
            } else if (index < 0) {
                currentIndex = totalImages - 1; // Retour à la dernière image
            } else {
                currentIndex = index; // Mise à jour de l'index courant
            }

            // Décalage pour afficher la bonne image
            const offset = -currentIndex * 100;
            document.querySelector('.carousel-images').style.transform = `translateX(${offset}%)`;
        }

        // Fonction pour afficher l'image suivante
        function nextImage() {
            showImage(currentIndex + 1);
        }

        // Fonction pour afficher l'image précédente
        function prevImage() {
            showImage(currentIndex - 1);
        }
    </script>

    <!-- Section de présentation -->
    <section class="presentation">
        <h1>Bienvenue chez <span style="color: orange;">RAPIDOS</span> !</h1>
        
        <p>
            RAPIDOS est un club de course à pied <strong>dynamique et convivial</strong>, ouvert à tous les passionnés de sport, quel que soit leur niveau.
        </p>

        <p>
            Basé en France, notre club a pour vocation de promouvoir un mode de vie actif et sain à travers des entraînements réguliers, des compétitions locales et nationales, ainsi que des événements sociaux.
        </p>

        <p>
            <strong>Que vous soyez un coureur débutant ou un athlète chevronné</strong>, vous trouverez votre place parmi nous. Rejoignez une communauté motivée et partagez des moments mémorables en alliant performance et plaisir.
        </p>

        <p>
            Chez RAPIDOS, la vitesse n’est pas qu’une question de chrono, mais aussi un <strong>état d’esprit</strong> !
        </p>
    </section>

    <!-- Section historique -->
    <section class="history">
        <h2>Notre histoire :</h2>
        <p>
            Fondé en <strong>2015</strong> par un groupe de passionnés de course à pied, RAPIDOS a rapidement évolué pour devenir un club incontournable de la région. 
        </p>
        <p>
            Parmi les événements marquants de notre histoire : l’organisation de notre <strong>premier marathon annuel en 2018</strong>, qui attire aujourd’hui des centaines de participants, et les victoires de nos membres lors de compétitions nationales. Grâce à notre engagement envers l’excellence et la convivialité, nous avons créé une véritable famille sportive au fil des années.
        </p>
    </section>
</div>
