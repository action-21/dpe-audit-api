# Moteur de calcul de la performance énergétique des logements (PCL) - API

> [!IMPORTANT]
> Ce dépôt est une base de code du projet accessible [ici](https://github.com/action-21/reno-audit).

## Installation

```
git clone https://github.com/action-21/reno-audit-api
cd reno-audit-api
composer install
symfony server:start
```

## Tests

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
# /etc/calculs/ventilation.yaml
performance:
  debit:
    - type_systeme: VENTILATION_NATURELLE
      mode_extraction: null
      mode_insufflation: HYGROREGLABLE
      presence_echangeur: null
      systeme_collectif: null
      annee_installation: 1900
      qvarep_conv: 2.23
      qvasouf_conv: 0
      smea_conv: 3
```
