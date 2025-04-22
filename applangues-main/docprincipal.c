#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#define MAX_MOTS 100 // Nombre maximum de mots dans le dictionnaire
#define MAX_UTILISATEURS 10 // Nombre maximal d'utilisateurs

// Structure pour un utilisateur
struct Utilisateur {
    char id[50];
    char mdp[50];
};

// Fonction pour lire les informations sur les utilisateurs depuis un fichier
int lireUtilisateurs(struct Utilisateur *utilisateur, char *nomFichier) {
    FILE *fichier = fopen("users.txt", "r");
    if (fichier == NULL) {
        perror("Erreur lors de l'ouverture du fichier");
        return 0; // Échec de la lecture
    }

    int nbUtilisateur = 0;

    while (fscanf(fichier, "%s %s", utilisateur[nbUtilisateur].id, utilisateur[nbUtilisateur].mdp) == 2) {
        nbUtilisateur++;
        if (nbUtilisateur >= MAX_UTILISATEURS) {
            break; // Limite atteinte
        }
    }

    fclose(fichier);
    return nbUtilisateur;
}

// Fonction pour écrire les informations sur les utilisateurs dans un fichier
void ecrireUtilisateurs(struct Utilisateur *utilisateur, int nbUtilisateur, char *nomFichier) {
    FILE *fichier = fopen("users.txt", "w");
    if (fichier == NULL) {
        perror("Erreur lors de l'ouverture du fichier");
        return; // Échec de l'écriture
    }

    for (int i = 0; i < nbUtilisateur; i++) {
        fprintf(fichier, "%s %s\n", utilisateur[i].id, utilisateur[i].mdp);
    }

    fclose(fichier);
}

// Fonction pour inscrire un nouvel utilisateur
void creerUtilisateur(struct Utilisateur *utilisateur, int *nbUtilisateur) {
    if (*nbUtilisateur < MAX_UTILISATEURS) {
        printf("Entrez un nouvel identifiant : ");
        scanf("%s", utilisateur[*nbUtilisateur].id);

        printf("Entrez un nouveau mot de passe : ");
        scanf("%s", utilisateur[*nbUtilisateur].mdp);

        (*nbUtilisateur)++;
        printf("Inscription réussie !\n");
    } else {
        printf("Le nombre maximal d'utilisateurs a été atteint. Vous ne pouvez pas ajouter plus d'utilisateurs.\n");
    }
}

// Fonction pour vérifier les informations d'identification de l'utilisateur
int authentifierUtilisateur(struct Utilisateur *utilisateur, int nbUtilisateur, char id[], char mdp[]) {
    for (int i = 0; i < nbUtilisateur; i++) {
        if (strcmp(id, utilisateur[i].id) == 0 &&
            strcmp(mdp, utilisateur[i].mdp) == 0) {
            return 1; // Authentification réussie
        }
    }
    return 0; // Authentification échouée
}

void afficherJeux() { // Affichage des différents jeux
    printf("Choisissez un jeux :\n");
    printf("1. Association de mots\n");
    printf("2. Vrai/Faux\n");
    printf("3. Traduction\n");
    printf("4. Trouver le thème \n");
    printf("5. Phrase à trous\n");
    printf("6. Afficher le dictionnaire de mots\n");
    printf("7. Ajouter un mot au dictionnaire\n");
    printf("0. Quitter\n");
    
}
void afficherThemes() { // Affichage des différents thèmes
    printf("Choisissez un thème:\n");
    printf("1. Animaux\n");
    printf("2. Fruits\n");
    printf("3. Couleurs\n");
    printf("4. Métiers\n");
    printf("5. Pays\n");
    printf("0. Quitter\n");
    
}

// Structure pour un mot
typedef struct {
    char francais[100];
    char anglais[100];
    char signification[250];
    char categorie[50];
    char prononciation[100];
} Mot;


// Ajouter un mot au dictionnaire
void ajouterMotdico(Mot *dictionnaire, int *taille) {
    if (*taille < MAX_MOTS) {
        printf("Entrez le nouveau mot : \n");
        scanf(" %s", dictionnaire[*taille].francais);

        printf("Entrez la traduction du mot : \n");
        scanf(" %s", dictionnaire[*taille].anglais);

        printf("Entrez la description du mot : \n");
        scanf(" %s", dictionnaire[*taille].signification);

        printf("Entrez le thème du mot : \n");
        scanf(" %s", dictionnaire[*taille].categorie);

        printf("Entrez la prononciation du mot : \n");
        scanf(" %s", dictionnaire[*taille].prononciation);

        (*taille)++;
        printf("Le mot a été ajouté avec succès !\n");
    } else {
        printf("Le dictionnaire est plein. Vous ne pouvez pas ajouter plus de mots.\n");
    }
}

// Fonction pour afficher le contenu du dictionnaire
void afficherDictionnaire(Mot dictionnaire[], int tailleDictionnaire) {
    printf("\nDictionnaire :\n");
    for (int i = 0; i < tailleDictionnaire; i++) {
        printf("Mot : %s\n", dictionnaire[i].francais);
        printf("Traduction : %s\n", dictionnaire[i].anglais);
        printf("Description : %s\n", dictionnaire[i].signification);
        printf("Thème : %s\n", dictionnaire[i].categorie);
        printf("Prononciation : %s\n", dictionnaire[i].prononciation);
        printf("--------------------------\n");
    }
}

void ajouterMot(Mot *dictionnaire, int *taille, Mot mot) {
    dictionnaire[(*taille)++] = mot;
}
void afficherMotsAleatoires(Mot *dictionnaire, int taille, int *indicesFrancais, int *indicesAnglais, int *correspondances) {
    // Mélanger les indices pour les mots français
    for (int i = taille - 1; i > 0; i--) {
        int r = rand() % (i + 1);
        int temp = indicesFrancais[i];
        indicesFrancais[i] = indicesFrancais[r];
        indicesFrancais[r] = temp;
    }

    // Mélanger les indices pour les mots anglais
    for (int i = taille - 1; i > 0; i--) {
        int r = rand() % (i + 1);
        int temp = indicesAnglais[i];
        indicesAnglais[i] = indicesAnglais[r];
        indicesAnglais[r] = temp;
    }

    // Afficher les mots mélangés et mettre à jour les correspondances
    printf("%-20s %-20s\n", "Francais", "Anglais");
    printf("-------------------- --------------------\n");
    for (int i = 0; i < taille; i++) {
        printf("%d. %-20s %d. %-20s\n", i + 1, dictionnaire[indicesFrancais[i]].francais, i + 1, dictionnaire[indicesAnglais[i]].anglais);
        correspondances[i] = indicesAnglais[i] + 1; // On stocke l'association correcte
    }
}


int verifierChoix(Mot *dictionnaire, int taille, int *indicesFrancais, int *indicesAnglais, int choixFrancais, int choixAnglais) {
    // Trouver les mots correspondants dans le dictionnaire en utilisant les indices mélangés
    Mot motFrancais = dictionnaire[indicesFrancais[choixFrancais - 1]];
    Mot motAnglais = dictionnaire[indicesAnglais[choixAnglais - 1]];

    // Vérifier si les mots correspondent
    return strcmp(motFrancais.anglais, motAnglais.anglais) == 0;
}



void jouerAssociation(Mot *dictionnaire, int taille) {
    int score = 0; // Score de l'utilisateur
    int choixFrancais, choixAnglais; // Indices choisis par l'utilisateur
    int tours = 10; // Nombre total de tours

    printf("Bienvenue au jeu d'association de mots!\n");

    srand(time(NULL));  // Initialiser la graine du générateur de nombres aléatoires

    // Tableaux pour stocker les indices mélangés et les correspondances
    int indicesFrancais[1000];
    int indicesAnglais[1000];
    int correspondances[1000];

    for (int i = 0; i < taille; i++) {
        indicesFrancais[i] = i;
        indicesAnglais[i] = i;
        correspondances[i] = i + 1;  // Initialiser les correspondances à l'indice lui-même + 1
    }

    for (int t = 0; t < tours; t++) {
        // Afficher la grille de mots de manière aléatoire
        afficherMotsAleatoires(dictionnaire, taille, indicesFrancais, indicesAnglais, correspondances);

        // Demander à l'utilisateur de choisir deux paires
        printf("\nChoisissez deux paires correspondantes (colonne français [espace] colonne anglais): ");
        scanf("%d %d", &choixFrancais, &choixAnglais);


        // Vérifier si les choix sont corrects
        if (verifierChoix(dictionnaire, taille, indicesFrancais, indicesAnglais, choixFrancais, choixAnglais)) {
            printf("Correct!\n");
            score++;
        } else {
            printf("Incorrect!\n");
        }
        printf("\n");


        for (int i = 0; i < taille; i++) {
            indicesFrancais[i] = i;
            indicesAnglais[i] = i;
            correspondances[i] = i + 1;
        }

        // Mélanger les indices pour les mots français
        for (int i = taille - 1; i > 0; i--) {
            int r = rand() % (i + 1);
            int temp = indicesFrancais[i];
            indicesFrancais[i] = indicesFrancais[r];
            indicesFrancais[r] = temp;
        }

        // Mélanger les indices pour les mots anglais
        for (int i = taille - 1; i > 0; i--) {
            int r = rand() % (i + 1);
            int temp = indicesAnglais[i];
            indicesAnglais[i] = indicesAnglais[r];
            indicesAnglais[r] = temp;
        }

        // Mélanger les correspondances (nouvelle partie)
        for (int i = taille - 1; i > 0; i--) {
            int r = rand() % (i + 1);
            int temp = correspondances[i];
            correspondances[i] = correspondances[r];
            correspondances[r] = temp;
        }
    }

    printf("Votre score final est : %d/%d\n", score, tours);
}

void jeuVraiFaux() { // Code du jeu vrai/faux 
    printf("Bienvenue dans le jeu Vrai/Faux !\n");
    printf("A vous de vérifier si ces phrases sont correctement écrites !\n");
    // Différentes phrases dans le jeu
    const char *phrases[] = {
        "The sky is blue. \n",
        "We is hungry. \n",
        "My kat is cute. \n",
        "I live in a house.\n"
    };

    // Bonne réponse pour chaque question
    const int reponses[] = {1, 0, 0, 1};

    // Nombre total de phrases
    const int nbPhrases = sizeof(phrases) / sizeof(phrases[0]);

    int score = 0;

    
    for (int i = 0; i < nbPhrases; ++i) {
        printf("%s\n", phrases[i]);

        // Demande à l'utilisateur de répondre 
        int reponse;
        printf("Votre réponse (1 pour Vrai, 0 pour Faux) : ");
        scanf("%d", &reponse);

        // Vérifie la réponse et ajuste le score
        if (reponse == reponses[i]) {
            printf("Correct !\n");
            score++;
        } else {
            printf("Incorrect. La réponse correcte est : %d\n", reponses[i]);
        }
    }

    // Affiche le score final
    printf("Votre score final : %d / %d\n", score, nbPhrases);
    printf("Merci d'avoir joué au jeu Vrai/Faux !\n");
}

void jeuTraduction() {
    printf("Bienvenue dans le jeu de Traduction !\n");
    // Initialisation du générateur de nombres aléatoires
    srand(time(NULL));

    // Phrases en français
    char phrases[3][50] = {"Le soleil brille", "C'est très drole", "J'adore l'école"};

    // Traductions en anglais correspondantes
    char traductions[3][50] = {"The sun is shining", "It is very funny", "I love school"};

    // Choisir aléatoirement une phrase
    //int indicePhrase = rand() % 3;
    const int nbPhrases = sizeof(phrases) / sizeof(phrases[0]);

    int score = 0;

    
    for (int i = 0; i < nbPhrases; ++i) {
        // Afficher la phrase en français
        printf("Traduisez la phrase suivante en anglais : %s\n", phrases[i]);

        // Utilisateur entre la traduction
        char reponse[100]; // Utilisation d'une taille plus grande pour éviter les dépassements
        scanf("%d", &reponse);
        fgets(reponse, sizeof(reponse), stdin);

        // Supprimer le retour à la ligne du buffer
        size_t len = strlen(reponse);
        if (len > 0 && reponse[len - 1] == '\n') {
            reponse[len - 1] = '\0';
        }

        // Compare la réponse avec la traduction correcte
        if (strcmp(reponse, traductions[i]) == 0) {                     // i peut être tranformer en indicePhrase si on fait une ohrase au lieu de toutes
            printf("Correct !\n");
        } else {
            printf("Incorrect. La réponse correcte est : %s\n", traductions[i]);
        }
    }

    // Affiche le score final
    printf("Votre score final : %d / %d\n", score, nbPhrases);
    printf("Merci d'avoir joué au jeu de Traduction !\n");
    
}



void jeuTextetheme() { // Code du jeu des textes à thème
    printf("Bienvenue dans le jeu Trouver le thème !\n");
    printf("A vous de vérifier si ces phrases sont correctement écrites !\n");
    // Différents textes dans le jeu 
    const char *textes[] = {
        "I am studying IT, I would like to become a web designer.\n",
        "I love fuchsia, it's my favorite color.\n", 
        "I would like to visit Italy one day. \n",
        "In a fruit salad you need peach and cherries otherwise it's inedible.\n"
    };

    // Bonne réponse pour chaque question
    const int reponses[] = {4, 3, 5, 2};

    // Nombre total de textes
    const int nbTextes = sizeof(textes) / sizeof(textes[0]);

    int score = 0;

    
    for (int i = 0; i < nbTextes; ++i) {
        printf("%s\n", textes[i]);

        // Demande à l'utilisateur de répondre 
        int reponse;
        printf("Votre réponse : (1 pour Animaux, 2 pour Fruits, 3 pour Couleurs, 4 pour Métiers, 5 pour Pays)\n");
        scanf("%d", &reponse);

        // Vérifie la réponse et ajuste le score
        if (reponse == reponses[i]) {
            printf("Correct !\n");
            score++;
        } 
        else {
            printf("Incorrect. La réponse correcte est : %d\n", reponses[i]);
        }
    }

    // Affiche le score final
    printf("Votre score final : %d / %d\n", score, nbTextes);
    printf("Merci d'avoir joué au jeu Trouver le thème !\n");
}

void jeuPhrasetrou() { // Code du jeu des phrases à trous
    printf("Bienvenue dans le jeu Phrases à trous!\n");
    printf("A vous de compléter les phrases par un des mots proposés !\n");
    // Différents textes dans le jeu 
    const char *textes[] = {
        "The .......  is shining.\n (1 pour sun, 2 pour cloud, 3 pour sky, 4 pour rain)\n",
        "The ...... are so delicious.\n (1 pour cactus, 2 pour smartphone, 3 pour pies, 4 pour table)\n", 
        "We go to the ...... .\n (1 pour floor tile, 2 pour ice rink, 3 pour furniture)\n",
        "What film .... you choose?.\n (1 pour do, 2 pour does)\n"
    };

    // Bonne réponse pour chaque question
    const int reponses[] = {1, 3, 2, 1};

    // Nombre total de textes
    const int nbTextes = sizeof(textes) / sizeof(textes[0]);

    int score = 0;

    
    for (int i = 0; i < nbTextes; ++i) {
        printf("%s\n", textes[i]);

        // Demande à l'utilisateur de répondre 
        int reponse;
        printf("Votre réponse : \n");
        scanf("%d", &reponse);

        // Vérifie la réponse et ajuste le score
        if (reponse == reponses[i]) {
            printf("Correct !\n");
            score++;
        } 
        else {
            printf("Incorrect. La réponse correcte est : %d\n", reponses[i]);
        }
    }

    // Affiche le score final
    printf("Votre score final : %d / %d\n", score, nbTextes);
    printf("Merci d'avoir joué au jeu Phrases à trous !\n");
}

int main() {
    // Déclaration des utilisateurs
    struct Utilisateur utilisateur[MAX_UTILISATEURS];
    int nbUtilisateur = 0;

    // Charger les utilisateurs depuis le fichier
    nbUtilisateur = lireUtilisateurs(utilisateur,"users.txt");

    char id[50];
    char mdp[50];
    int choix;

    
        printf("1. Se connecter\n");
        printf("2. S'inscrire\n");
        printf("0. Quitter\n");
        printf("Choisissez une option : ");
        scanf("%d", &choix);

        switch (choix) {
            case 1:
            // Demander à l'utilisateur de se connecter
            printf("Entrez votre identifiant : ");
            scanf("%s", id);

            printf("Entrez votre mot de passe : ");
            scanf("%s", mdp);

            // Vérifier les informations d'identification
            if (authentifierUtilisateur(utilisateur, nbUtilisateur, id, mdp)) {
                printf("Connexion réussie!\n");
                while (1){
                    Mot dictionnaire[MAX_MOTS]; // Dictionnaire de mots
                    int tailleDictionnaire = 0;
                    int choixTheme;
                    int choixJeux;        
                    afficherJeux();
                    printf("Choisissez le jeux souhaité : ");
                    scanf("%d", &choixJeux); // Traite le choix de l'utilisateur
                    switch (choixJeux) {
                        case 0:
                            printf("Jeu quitté\n");
                        break; // Arrête le programme
                        case 1:
                            printf("Vous avez choisi le jeux : Association de mots \n");
                            afficherThemes();
                            printf("Choisissez le thème souhaité : ");
                            scanf("%d", &choixTheme);
                                switch (choixTheme) {
                                    case 0:
                                        printf("Jeu quitté\n");
                                    break; // Arrête le programme
                                    case 1:
                                        printf("Vous avez choisi le thème des animaux.\n");
                                        Mot animaux[] = {
                                            {"Chien", "Dog", "Animal domestique", "Animaux", "shee-an"},
                                            {"Chat", "Cat", "Animal domestique", "Animaux", "kat"},
                                            {"Lion", "Lion", "Grand félin", "Animaux", "lye-uhn"},
                                            {"Éléphant", "Elephant", "Gros mammifère", "Animaux", "el-uh-fuhnt"},
                                            {"Serpent", "Snake", "Animal reptilien", "Animaux", "sneyk"},
                                            {"Oiseau", "Bird", "Animal ailé", "Animaux", "burd"},
                                            {"Poisson", "Fish", "Animal aquatique", "Animaux", "fish"},
                                        };
                                        for (int i = 0; i < sizeof(animaux) / sizeof(animaux[0]); i++) {
                                            ajouterMot(dictionnaire, &tailleDictionnaire, animaux[i]);
                                        }
                                        jouerAssociation(dictionnaire, tailleDictionnaire);

                                    break;
                                    case 2:
                                        printf("Vous avez choisi le thème des fruits.\n");
                                        Mot fruits[] = {
                                            {"Pomme", "Apple", "Fruit", "Fruits", "ap-uhl"},
                                            {"Banane", "Banana", "Fruit", "Fruits", "buh-nan-uh"},
                                            {"Orange", "Orange", "Fruit", "Fruits", "or-inj"},
                                            {"Raisin", "Grapes", "Fruit", "Fruits", "greyps"},
                                            {"Fraise", "Strawberry", "Fruit", "Fruits", "straw-ber-ee"},
                                            {"Cerise", "Cherry", "Fruit", "Fruits", "cher-ee"},
                                            {"Pêche", "Peach", "Fruit", "Fruits", "peech"},
                                        };
                                        for (int i = 0; i < sizeof(fruits) / sizeof(fruits[0]); i++) {
                                            ajouterMot(dictionnaire, &tailleDictionnaire, fruits[i]);
                                        }
                                        jouerAssociation(dictionnaire, tailleDictionnaire);

                                    break;
                                    case 3:
                                        printf("Vous avez choisi le thème des couleurs.\n");
                                        Mot couleurs[] = {
                                            {"Rouge", "Red", "Couleur", "Couleurs", "red"},
                                            {"Bleu", "Blue", "Couleur", "Couleurs", "bloo"},
                                            {"Jaune", "Yellow", "Couleur", "Couleurs", "yel-oh"},
                                            {"Vert", "Green", "Couleur", "Couleurs", "green"},
                                            {"Orange", "Orange", "Couleur", "Couleurs", "or-inj"},
                                            {"Violet", "Purple", "Couleur", "Couleurs", "pur-puhl"},
                                            {"Rose", "Pink", "Couleur", "Couleurs", "pingk"},
                                        };
                                        for (int i = 0; i < sizeof(couleurs) / sizeof(couleurs[0]); i++) {
                                            ajouterMot(dictionnaire, &tailleDictionnaire, couleurs[i]);
                                        }
                                        jouerAssociation(dictionnaire, tailleDictionnaire);
                                
                                    break;
                                    case 4:
                                        printf("Vous avez choisi le thème des métiers.\n");
                                        Mot metiers[] = {
                                            {"Médecin", "Doctor", "Métier", "Métiers", "dok-ter"},
                                            {"Policier", "Police officer", "Métier", "Métiers", "puh-leece aw-fuh-ser"},
                                            {"Pompier", "Firefighter", "Métier", "Métiers", "fahy-er-fahy-ter"},
                                            {"Professeur", "Teacher", "Métier", "Métiers", "tee-cher"},
                                            {"Ingénieur", "Engineer", "Métier", "Métiers", "en-juh-neer"},
                                            {"Avocat", "Lawyer", "Métier", "Métiers", "loi-er"},
                                            {"Scientifique", "Scientist", "Métier", "Métiers", "sahy-uhn-tist"},
                                        };
                                        for (int i = 0; i < sizeof(metiers) / sizeof(metiers[0]); i++) {
                                            ajouterMot(dictionnaire, &tailleDictionnaire, metiers[i]);
                                        }
                                        jouerAssociation(dictionnaire, tailleDictionnaire);

                                    break;
                                    case 5:
                                        printf("Vous avez choisi le thème des pays.\n");
                                        Mot pays[] = {
                                            {"France", "France", "Pays", "Pays", "frans"},
                                            {"États-Unis", "United States", "Pays", "Pays", "yoo-nahy-tid stayts"},
                                            {"Japon", "Japan", "Pays", "Pays", "juh-pan"},
                                            {"Brésil", "Brazil", "Pays", "Pays", "bruh-zil"},
                                            {"Australie", "Australia", "Pays", "Pays", "aw-streyl-yuh"},
                                            {"Inde", "India", "Pays", "Pays", "in-dee-uh"},
                                            {"Canada", "Canada", "Pays", "Pays", "kan-uh-duh"},
                                        };
                                        for (int i = 0; i < sizeof(pays) / sizeof(pays[0]); i++) {
                                            ajouterMot(dictionnaire, &tailleDictionnaire, pays[i]);
                                        }
                                        jouerAssociation(dictionnaire, tailleDictionnaire);

                                    break;
                                }
                        break;
                                            
                        case 2:
                            printf("Vous avez choisi le jeu : Vrai/Faux\n");
                            jeuVraiFaux();
                        break;
                        case 3:
                            printf("Vous avez choisi le jeu : Traduction \n");
                            jeuTraduction();
                        break;
                        case 4:
                            printf("Vous avez choisi le jeu : Trouver le thème \n");
                            jeuTextetheme();
                        break;
                        case 5:
                            printf("Vous avez choisi le jeu : Phrase à trous\n");
                            jeuPhrasetrou();
                        break;
                        case 6:
                            afficherDictionnaire(dictionnaire, tailleDictionnaire);
                        break;
                        case 7:
                            ajouterMotdico(dictionnaire, &tailleDictionnaire);
                        break;
                        default:
                            printf("Choix invalide.\n");
                        }
                    }
                }
                    else {
                    printf("Échec de la connexion. Vérifiez vos informations d'identification ou inscrivez-vous.\n");
                    }
                break;
                
            case 2:
            // Inscrire un nouvel utilisateur
            creerUtilisateur(utilisateur, &nbUtilisateur);

            // Enregistrer les utilisateurs mis à jour dans le fichier
            ecrireUtilisateurs(utilisateur, nbUtilisateur,"users.txt");
            break;
            default:
                printf("Choix invalide.\n");
        }
        
    return 0; 
} 
