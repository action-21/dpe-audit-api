# Moteur de calcul de la performance énergétique des logements (PCL) - API

> [!IMPORTANT]
> Ce dépôt est une base de code du projet accessible [ici](https://github.com/action-21/reno-audit).

## Roadmap

- Enveloppe
  - [x] Modélisation
  - [x] Calcul
  - [x] Documentation
- Ventilation
  - [x] Modélisation
  - [x] Calcul
  - [x] Documentation
- Chauffage
  - [x] Modélisation
  - [x] Calcul
  - [] Documentation
- Eau chaude sanitaire
  - [x] Modélisation
  - [x] Calcul
  - [x] Documentation
- Refroidissement
  - [x] Modélisation
  - [x] Calcul
  - [x] Documentation
- Eclairage
  - [x] Modélisation
  - [x] Calcul
  - [x] Documentation
- Production
  - [x] Modélisation
  - [] Calcul
  - [] Documentation

### Tests end-to-end

On fait tourner le moteur de calcul sur la base d'un échantillon de données issu de l'[opendata de l'ADEME](https://data.ademe.fr/datasets/dpe-v2-logements-existants) et on compare les données de sortie.

#### Échantillonage

- 10 échantillons par zone climatique
- 1 échantillon par période de construction
- Intervalle de dates d'établissement à 30 jours déterminé aléatoirement pour assurer le renouvellement de l'échantillonage

#### Données de sortie

- Déperditions des murs
- Déperditions des planchers bas
- Déperditions des planchers hauts
- Déperditions des baies
- Déperditions des portes
- Déperditions par nouvellement d'air
- Déperditions de l'enveloppe
- Surface sud équivalente
- Besoin de chauffage par scénario d'usage
- Besoin d'eau chaude sanitaire par scénario d'usage
- Besoin de refroidissement par scénario d'usage
- Consommation de chauffage par scénario d'usage
- Consommation d'eau chaude sanitaire par scénario d'usage
- Consommation de refroidissement par scénario d'usage
- Consommation d'éclairage
- Consommation des auxiliaires par scénario d'usage
- Consommation finale par scénario d'usage
- Consommation primaire par scénario d'usage
- Emission de gaz à effet de serre par scénario d'usage

### Tests unitaires des calculs intermédiaires

L'ensemble des calculs intermédiaires de la méthode conventionnelle est intégré aux différents moteurs de calcul sous la forme de fonctions simples sans effet de bord afin de faciliter les tests.

Les tests sont écrits pour chaque moteur de calcul au format yaml dans le dossier /etc/calculs :

```
# /etc/calculs/baie.ensoleillement.yaml

tests:
  sse:
    - test: Un test unitaire de la fonction sse
      resultat: 0.56
      surface: 2
      sw: 0.5
      fe: 0.8
      c1: 0.7
```
