import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { OBJLoader } from 'three/addons/loaders/OBJLoader.js'; // Remplacer GLTFLoader par OBJLoader
// import { Coordinates } from '../lib/Coordinates.js';
import { GUI } from 'https://cdn.jsdelivr.net/npm/lil-gui@0.17/+esm';


// var camera, renderer;
// window.scene = new THREE.Scene();
var camera, renderer, scene;
scene = new THREE.Scene();
var cameraControls;
var clock = new THREE.Clock();
var transparentCube;
var light1, light2;
var params = {
    ambientLightIntensity: 3,
    light1Intensity: 4,
    fogDensity: 0.001,
    enableShadows: false,
    enableFog: true,
    cameraX: -400,
    cameraY: 150,
    cameraZ: -20,
    light1X: 0,
    light1Y: 300,
    light1Z: 0,
    shadowBias: -0.0005,
    shadowRadius: 2,
    showHelpers: false
};

const objLoader = new OBJLoader(); // Utilisation de OBJLoader au lieu de GLTFLoader

function fillScene() {
    scene.fog = new THREE.FogExp2(0xB0BEC5, params.fogDensity);

    // LIGHTS avec configuration précise des ombres
    const ambientLight = new THREE.AmbientLight(0x888888, params.ambientLightIntensity);
    scene.add(ambientLight);

    // Lumière directionnelle principale pour les ombres
    light1 = new THREE.DirectionalLight(0xFFFFFF, params.light1Intensity);
    light1.position.set(params.light1X, params.light1Y, params.light1Z);
    light1.castShadow = params.enableShadows;
    scene.add(light1);

    // Configuration des paramètres d'ombre
    light1.shadow.mapSize.width = 2048;  // Haute résolution pour des ombres nettes
    light1.shadow.mapSize.height = 2048;
    light1.shadow.camera.near = 10;
    light1.shadow.camera.far = 1000;
    light1.shadow.bias = params.shadowBias; // Contrôlable via GUI
    light1.shadow.radius = params.shadowRadius; // Contrôlable via GUI

    // Configuration critique: définir la boîte de la caméra d'ombre
    light1.shadow.camera.left = -500;
    light1.shadow.camera.right = 500;
    light1.shadow.camera.top = 500;
    light1.shadow.camera.bottom = -500;

   

    // Coordinates.drawGround({ size: 800 });

    // Cube Transparent au Centre
    var transparentMaterial = new THREE.MeshBasicMaterial({
        transparent: true,
        opacity: 0
    });
    var cubeGeometry = new THREE.BoxGeometry(100, 100, 100);
    transparentCube = new THREE.Mesh(cubeGeometry, transparentMaterial);
    transparentCube.position.set(0, -100, 0);

    scene.add(transparentCube);

    // Sol
    var floorTexture = new THREE.TextureLoader().load('elements/sol_boiserie.jpg');
    var floorMaterial = new THREE.MeshStandardMaterial({ map: floorTexture });
    var floor = new THREE.Mesh(new THREE.PlaneGeometry(400, 400), floorMaterial);
    floor.rotation.x = -Math.PI / 2;
    floor.position.y = -100;
    floor.receiveShadow = true; // Le sol reçoit des ombres
    scene.add(floor);

    // Murs
    var wallTexture = new THREE.TextureLoader().load('elements/mur_bleu.jpg');
    var wallMaterial = new THREE.MeshStandardMaterial({ map: wallTexture
    });

    var backWall = new THREE.Mesh(new THREE.PlaneGeometry(400, 300), wallMaterial);
    backWall.position.set(0, 50, -200);
    scene.add(backWall);

    var leftWall = new THREE.Mesh(new THREE.PlaneGeometry(400, 300), wallMaterial);
    leftWall.rotation.y = Math.PI / 2;
    leftWall.position.set(-200, 50, 0);
    scene.add(leftWall);

    var rightWall = new THREE.Mesh(new THREE.PlaneGeometry(400, 300), wallMaterial);
    rightWall.rotation.y = -Math.PI / 2;
    rightWall.position.set(200, 50, 0);
    scene.add(rightWall);

    var frontWall = new THREE.Mesh(new THREE.PlaneGeometry(400, 300), wallMaterial);
    frontWall.rotation.y = Math.PI;
    frontWall.position.set(0, 50, 200);
    scene.add(frontWall);

    // Lit 
    // Lit avec texture et matériaux différents
objLoader.load('elements/wooden_bed.obj', function (object) {
    let bed = object;
    
    // Textures
    var woodTexture = new THREE.TextureLoader().load('elements/sol_boiserie.jpg');
    
    // Matériaux
    const woodMaterial = new THREE.MeshStandardMaterial({
        map: woodTexture,
        color: 0x8B4513,
        roughness: 0.8,
        metalness: 0.2
    });
    
    const redMaterial = new THREE.MeshStandardMaterial({
        color: 0xAA0000, // Rouge foncé
        roughness: 0.7,
        metalness: 0.1
    });
    
    const whiteMaterial = new THREE.MeshStandardMaterial({
        color: 0xFFFFFF, // Blanc
        roughness: 0.5,
        metalness: 0.0
    });
    
    // Compter les meshes pour identifier différentes parties
    let meshCount = 0;
    
    // Appliquer les matériaux en fonction de l'index des meshes
    bed.traverse(function (child) {
        if (child instanceof THREE.Mesh) {
            if (meshCount % 3 === 0) {
                child.material = whiteMaterial; // Cadre
            } else if (meshCount % 3 === 1) {
                child.material = woodMaterial; // Couverture
            } else {
                child.material = redMaterial; // Oreillers
            }
            meshCount++;
        }
    });

    enableShadows(bed);
    
    bed.position.set(130 - 50, -100, 127);
    bed.scale.set(100, 100, 100);
    bed.rotation.y = 11;
    bed.castShadow = true;
    bed.receiveShadow = true;
    scene.add(bed);
});

    // Chaise
    objLoader.load('elements/wooden_chair.obj', function (object) {
        let chair1 = object.clone();
        let chair2 = object.clone();

        // Appliquer le même matériau bois que la table aux chaises
        const woodTexture = new THREE.TextureLoader().load('elements/sol_boiserie.jpg');
        const chairMaterial = new THREE.MeshStandardMaterial({
            map: woodTexture,      // Utiliser la texture pour le bois
            color: 0x8B4513,       // Teinte supplémentaire (peut être ajustée)
            roughness: 0.8,
            metalness: 0.2
        });
        
        // Appliquer le matériau à chaque chaise
        chair1.traverse(function (child) {
            if (child instanceof THREE.Mesh) {
                child.material = chairMaterial;
            }
        });
        
        chair2.traverse(function (child) {
            if (child instanceof THREE.Mesh) {
                child.material = chairMaterial;
            }
        });

        enableShadows(chair1);
        enableShadows(chair2);
        
        chair1.position.set(100, -100, 0); // Première chaise à côté du lit
        chair1.rotation.y = 12;
        chair2.position.set(-100, -100, -150); // Deuxième chaise à côté de la première
        chair2.rotation.y = 12;
        
        chair1.scale.set(100, 100, 100);
        chair2.scale.set(100, 100, 100);

        scene.add(chair1);
        scene.add(chair2);


        // Charger la table au format OBJ
        const objLoader = new OBJLoader();
        objLoader.load('elements/wooden_table.obj', function (object) {
            // Appliquer un matériau à tous les enfants de l'objet
            object.traverse(function (child) {
                if (child instanceof THREE.Mesh) {
                    child.material = new THREE.MeshStandardMaterial({
                        color: 0x8B4513, // Couleur bois
                        roughness: 0.8,
                        metalness: 0.2
                    });
                }
            });
            
            // Ajuster la position, la taille et la rotation de la table
            object.position.set(100, -100, -110); // Position entre les chaises
            object.scale.set(100, 100, 100); // Échelle à ajuster selon le modèle
            object.rotation.y = Math.PI / 4;
            enableShadows(object);
            // Ajouter la table à la scène
            scene.add(object);


            // Charger la théière et la placer sur la table
            objLoader.load('elements/tea.obj', function (teapot) {
                // Appliquer un matériau à la théière
                teapot.traverse(function (child) {
                    if (child instanceof THREE.Mesh) {
                        child.material = new THREE.MeshStandardMaterial({
                            color: 0x0000FF, // Couleur bleu
                            roughness: 0.2,
                            metalness: 0.8
                        });
                    }
                });
                
                // Positionner la théière sur la table
                teapot.position.set(100, 5, -110); // Même position X/Z que la table mais plus haut en Y
                teapot.scale.set(10, 10, 10); // Échelle appropriée pour la théière
                teapot.rotation.y = Math.PI / 4; // Même rotation que la table
                enableShadows(teapot);
                // Ajouter la théière à la scène
                scene.add(teapot);



                // Charger le vase et le placer à côté de la théière
                objLoader.load('elements/vase.obj', function (vase) {
                    // Appliquer un matériau au vase
                    vase.traverse(function (child) {
                        if (child instanceof THREE.Mesh) {
                            child.material = new THREE.MeshStandardMaterial({
                                color: 0x0000FF,
                                roughness: 0.1,
                                metalness: 0.2
                            });
                        }
                    });
                    
                    enableShadows(vase);
                    // Positionner le vase à côté de la théière sur la table
                    vase.position.set(80, -16, -130); // Position décalée par rapport à la théière
                    vase.scale.set(1, 1, 1); // Ajuster l'échelle selon la taille du modèle
                    vase.rotation.x = -(Math.PI / 2); // 90 degrés
                    
                    // Ajouter le vase à la scène
                    scene.add(vase);

                    // Créer un verre simple avec une géométrie de cylindre
                    const glassGeometry = new THREE.CylinderGeometry(3, 2.5, 8, 16);
                    const glassMaterial = new THREE.MeshPhysicalMaterial({
                        color: 0x0066FF,        // Couleur bleue
                        transparent: true,       // Transparent pour l'effet verre
                        opacity: 0.6,           // Semi-transparent
                        roughness: 0.1,         // Lisse
                        metalness: 0.0,         // Non métallique
                        clearcoat: 1.0,         // Effet de vernis
                        clearcoatRoughness: 0.1 // Vernis lisse
                    });
                    
                    const glass = new THREE.Mesh(glassGeometry, glassMaterial);
                    
                    // Positionner le verre à côté du vase sur la table
                    glass.position.set(65, -13, -130); // Ajusté pour être à côté du vase
                    glass.scale.set(1, 1, 1);
                    enableShadows(glass);
                    // Ajouter le verre à la scène
                    scene.add(glass);



                    // Ajouter deux bougies sur la table
                    objLoader.load('elements/candle.obj', function (candle) {
                        // Appliquer un matériau à la bougie
                        candle.traverse(function (child) {
                            if (child instanceof THREE.Mesh) {
                                child.material = new THREE.MeshStandardMaterial({
                                    color: 0xF5DEB3, // Couleur crème
                                    roughness: 0.7,
                                    metalness: 0.1
                                });
                            }
                        });
                        
                        // Créer un clone pour la deuxième bougie
                        const candle1 = candle;
                        const candle2 = candle.clone();
                        
                        // Positionner les bougies sur la table
                        candle1.position.set(110, -16, -90); // Premier emplacement
                        candle1.scale.set(3, 3, 3); // Ajuster la taille selon le modèle
                        candle1.rotation.y = Math.PI / 6; // Légère rotation
                        
                        candle2.position.set(110, -16, -100); // Deuxième emplacement
                        candle2.scale.set(3, 3, 3);
                        candle2.rotation.y = -Math.PI / 8; // Rotation différente
                        enableShadows(candle1);
                        enableShadows(candle2);
                        // Ajouter les bougies à la scène
                        scene.add(candle1);
                        scene.add(candle2);

                        // *** Ajouter des sprites de flamme au-dessus des bougies ***
                        const flame1 = createFlameSprite(110, 8, -90);
                        const flame2 = createFlameSprite(110, 8, -100);
                        scene.add(flame1);
                        scene.add(flame2);

                        // Animation des flammes
                        function animateFlames() {
                            // Variation légère de la taille pour simuler le mouvement
                            flame1.scale.x = 5 + Math.sin(Date.now() * 0.01) * 0.5;
                            flame1.scale.y = 8 + Math.sin(Date.now() * 0.01) * 0.5;
                            flame2.scale.x = 5 + Math.sin(Date.now() * 0.01 + 1.5) * 0.5;
                            flame2.scale.y = 8 + Math.sin(Date.now() * 0.01 + 1.5) * 0.5;
                            requestAnimationFrame(animateFlames);
                        }
                        animateFlames();
                    });                    
                });
            });
        });
    });


    // Fenêtre 
    var windowTexture = new THREE.TextureLoader().load('elements/window.jpg');
    var windowMaterial = new THREE.MeshStandardMaterial({ map: windowTexture });
    var window = new THREE.Mesh(new THREE.PlaneGeometry(75, 125), windowMaterial);
    window.position.set(199, 60, -80);
    window.rotation.y = 11;
    scene.add(window);

    // Portes face à face
    var doorTexture = new THREE.TextureLoader().load('elements/porte.jpg');
    var doorMaterial = new THREE.MeshStandardMaterial({ map: doorTexture });

    var door1 = new THREE.Mesh(new THREE.PlaneGeometry(150, 250), doorMaterial);
    door1.position.set(-60, 25, 199);
    door1.rotation.y = 22;
    door1.castShadow = true; // Activer la projection d'ombre
    door1.receiveShadow = true; // Activer la réception d'ombre
    scene.add(door1);

    var door2 = new THREE.Mesh(new THREE.PlaneGeometry(150, 250), doorMaterial);
    door2.position.set(0, 25, -199);
    door2.castShadow = true; // Activer la projection d'ombre
    door2.receiveShadow = true; // Activer la réception d'ombre
    scene.add(door2);

    // droite 2
    const painting1 = createWallPainting(35, 25, 60, 80, 198, -Math.PI, 0x427F22);
    scene.add(painting1);

    // droite 1
    const painting2 = createWallPainting(35, 25, 120, 80, 198, -Math.PI, 0x659A87);
    scene.add(painting2);

    // au dessus lit
    const painting3 = createWallPainting(70, 40, 198, 100, 100, -Math.PI/2, 0xf1aa9b);
    scene.add(painting3);

    // gauche fenetre
    const painting4 = createWallPainting(20, 70, 198, 80, -160, -Math.PI/2, 0xCDBC9D);
    scene.add(painting4);

    //droite 3
    const painting5 = createWallPainting(50, 20, 60, 20, 198, -Math.PI, 0xE3CF8F, false);
    scene.add(painting5);

    //droite 4
    const painting6 = createWallPainting(20, 30, 120, 20, 198, -Math.PI, 0xE3CF8F, false);
    scene.add(painting6);

    // porte manteau
    const painting7 = createWallPainting(150, 10, 198, 40, 120, -Math.PI/2, 0x8B4513, false);
    scene.add(painting7);

   // Remplacer le cube blanc par un cube avec specular maps
    var cubeGeometry = new THREE.BoxGeometry(30, 200, 30); // Dimensions du cube long avec plus de profondeur

    // Charger les textures pour le cube
    const marbleTexture = new THREE.TextureLoader().load('elements/Presentation1.jpg'); // Utiliser une texture de marbre
    const marbleSpecularMap = new THREE.TextureLoader().load('elements/noisy-background.jpg'); // Carte spéculaire
    const marbleNormalMap = new THREE.TextureLoader().load('elements/specular.jpg'); // Carte normale

    // Créer un matériau avec specular maps
    var cubeMaterial = new THREE.MeshPhongMaterial({ 
        map: marbleTexture,             // Texture diffuse
        specularMap: marbleSpecularMap, // *** Specular map appliquée ici ***
        specular: 0xffffff,             // Couleur spéculaire 
        shininess: 100,                  // Brillance
        normalMap: marbleNormalMap,     // Normal map
        normalScale: new THREE.Vector2(1, 1)
    });

    var cube = new THREE.Mesh(cubeGeometry, cubeMaterial);
    cube.position.set(110, 20, -199); 
    cube.rotation.y = Math.PI/4; // Orientation à 45° vers la droite
    enableShadows(cube);
    scene.add(cube);


   // Remplacer votre fonction createFlameSprite par celle-ci
    function createFlameSprite(posX, posY, posZ) {
        // Création d'une flamme avec géométrie au lieu d'un sprite
        const flameGeometry = new THREE.ConeGeometry(1, 1, 5);
        const flameMaterial = new THREE.MeshBasicMaterial({
            color: 0xffaa00,
            transparent: true,
            opacity: 0.8,
            emissive: 0xffaa00,
            emissiveIntensity: 1.0
        });
        
        const flame = new THREE.Mesh(flameGeometry, flameMaterial);
        flame.position.set(posX, posY, posZ);
        
        // Ajouter une lumière ponctuelle plus intense
        const flameLight = new THREE.PointLight(0xff9900, 2, 50);
        flameLight.position.copy(flame.position);
        scene.add(flameLight);
        
        return flame;
    }
}

// Fonction améliorée pour créer des tableaux avec couleurs personnalisables
function createWallPainting(width, height, posX, posY, posZ, rotY, color, withRope = true) {
    // Groupe contenant le tableau et la corde
    const paintingGroup = new THREE.Group();
    
    // Créer le cadre du tableau - toujours en bois
    const frameGeometry = new THREE.BoxGeometry(width + 5, height + 5, 2);
    const frameMaterial = new THREE.MeshStandardMaterial({ 
        color: 0x8B4513, // Couleur bois standard
        roughness: 0.7,
        metalness: 0.2
    });
    const frame = new THREE.Mesh(frameGeometry, frameMaterial);
    
    // Créer la toile du tableau avec la couleur passée en paramètre
    const canvasGeometry = new THREE.PlaneGeometry(width, height);
    const canvasMaterial = new THREE.MeshStandardMaterial({ 
        color: color,       // Utilise le paramètre couleur
        roughness: 0.9,     // Surface mate comme une toile
        metalness: 0.0,     // Pas de métallicité pour une peinture
        emissive: color,    // Légère émission pour donner plus de profondeur
        emissiveIntensity: 0.1
    });
    
    const canvas = new THREE.Mesh(canvasGeometry, canvasMaterial);
    canvas.position.z = 1.1; // Légèrement devant le cadre
    
    // Ajouter le cadre et la toile au groupe
    paintingGroup.add(frame);
    paintingGroup.add(canvas);
    
    if (withRope) {
        // Créer la corde pour suspendre le tableau
        const ropeGeometry = new THREE.BufferGeometry();
        const ropePoints = [
            new THREE.Vector3(-width/2 + 5, height/2, 0),
            new THREE.Vector3(0, height/2 + 10, 0),
            new THREE.Vector3(width/2 - 5, height/2, 0)
        ];
        ropeGeometry.setFromPoints(ropePoints);
        const ropeMaterial = new THREE.LineBasicMaterial({ color: 0x5A3A22, linewidth: 2 });
        const rope = new THREE.Line(ropeGeometry, ropeMaterial);
        
        // Ajouter la corde au groupe
        paintingGroup.add(rope);
    }
    
    // Positionner le tableau sur le mur
    paintingGroup.position.set(posX, posY, posZ);
    paintingGroup.rotation.y = rotY || 0;
    
    // Activer les ombres
    paintingGroup.traverse(function(child) {
        if (child instanceof THREE.Mesh) {
            child.castShadow = true;
            child.receiveShadow = true;
        }
    });
    
    return paintingGroup;
}

// Fonction utilitaire pour activer les ombres sur un objet et tous ses enfants
function enableShadows(object) {
    object.traverse(function(child) {
        if (child instanceof THREE.Mesh) {
            child.castShadow = true;
            child.receiveShadow = true;
        }
    });
    return object; // Retourner l'objet pour permettre le chaînage
}

// Ajouter une fonction pour créer l'interface GUI
function createGUI() {
    const gui = new GUI({ title: 'Contrôles' });
    // Ajouter un dossier pour les positions des lumières
    const light1PosFolder = gui.addFolder('Lumières');
    light1PosFolder.add(params, 'light1X', -600, 600)
        .name('X')
        .onChange(value => {
            light1.position.x = value;
            // Mettre à jour les helpers
            scene.children.forEach(child => {
                if (child instanceof THREE.CameraHelper && child.camera === light1.shadow.camera) {
                    child.update();
                }
            });
        });
    
    light1PosFolder.add(params, 'light1Y', 0, 600)
        .name('Y')
        .onChange(value => {
            light1.position.y = value;
            scene.children.forEach(child => {
                if (child instanceof THREE.CameraHelper && child.camera === light1.shadow.camera) {
                    child.update();
                }
            });
        });
    
    light1PosFolder.add(params, 'light1Z', -600, 600)
        .name('Z')
        .onChange(value => {
            light1.position.z = value;
            scene.children.forEach(child => {
                if (child instanceof THREE.CameraHelper && child.camera === light1.shadow.camera) {
                    child.update();
                }
            });
        });
    
       
    light1PosFolder.add(params, 'light1Intensity', 0, 10)
        .name('Intensité')
        .onChange(value => {
            light1.intensity = value;
        });
        
    // Dossier pour les effets
    const effectsFolder = gui.addFolder('Effets');
    effectsFolder.add(params, 'enableShadows')
        .name('Ombres')
        .onChange(value => {
            renderer.shadowMap.enabled = value;

            light1.castShadow = value;
            light2.castShadow = value;
        });
        
    effectsFolder.add(params, 'enableFog')
        .name('Brouillard')
        .onChange(value => {
            if (value) {
                scene.fog = new THREE.FogExp2(0xB0BEC5, params.fogDensity);
            } else {
                scene.fog = null;
            }
        });
        
    effectsFolder.add(params, 'fogDensity', 0, 0.01)
        .name('Densité brouillard')
        .onChange(value => {
            if (scene.fog) {
                scene.fog.density = value;
            }
        });
    
    // Dossier pour la caméra
    const cameraFolder = gui.addFolder('Caméra');
    cameraFolder.add(params, 'cameraX', -600, 600)
        .name('Position X')
        .onChange(value => {
            camera.position.x = value;
        });
        
    cameraFolder.add(params, 'cameraY', 0, 300)
        .name('Position Y')
        .onChange(value => {
            camera.position.y = value;
        });
        
    cameraFolder.add(params, 'cameraZ', -600, 600)
        .name('Position Z')
        .onChange(value => {
            camera.position.z = value;
        });
    
    return gui;
}



function init() {
    var canvasWidth = 900;
    var canvasHeight = 500;
    var canvasRatio = canvasWidth / canvasHeight;

    // RENDERER
    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.gammaInput = true;
    renderer.gammaOutput = true;
    renderer.setSize(canvasWidth, canvasHeight);
    renderer.setClearColor(0xAAAAAA, 1.0);

    // Activer les ombres
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;

    // CAMERA
    camera = new THREE.PerspectiveCamera(45, canvasRatio, 1, 4000);
    camera.position.set(-400, 150, -20);
    camera.lookAt(new THREE.Vector3(0, 0, 0));

    // CONTROLS
    cameraControls = new OrbitControls(camera, renderer.domElement);
    cameraControls.enableDamping = true;
    cameraControls.dampingFactor = 0.05;
    cameraControls.rotateSpeed = 1.0;

    createGUI();

}

function addToDOM() {
    var container = document.getElementById('webGL');
    var canvas = container.getElementsByTagName('canvas');
    if (canvas.length > 0) {
        container.removeChild(canvas[0]);
    }
    container.appendChild(renderer.domElement);
}

function animate() {
    requestAnimationFrame(animate);
    render();
}

function render() {
    var delta = clock.getDelta();
    cameraControls.update();
    renderer.render(scene, camera); // CORRECTION CRITIQUE
}

try {
    init();
    fillScene();
    animate();
    addToDOM();
} catch (e) {
    console.error("Erreur lors du rendu:", e);
}

