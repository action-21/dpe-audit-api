# @renolab/audit-api

API 

## Roadmap

### Domaine métier

- [x] Baie
- [x] Climatisation
- [x] Local non chauffé
- [x] ECS
- [x] Masque lointain
- [x] Masque proche
- [x] Mur
- [x] Photovoltaïque
- [x] Plancher haut
- [x] Plancher intermédaire
- [x] Pont Thermique
- [x] Porte
- [x] Refend
- [x] Réseau de chaleur
- [x] Ventilation

### Fonctionnalités

- [] Simulation d'un audit depuis un audit existant (observatoire DPE-Audit)
```mermaid
sequenceDiagram
    Utilisateur->>Simulateur DPE-Audit: Existe-t-il un audit énergétique pour cette adresse ?
    Simulateur DPE-Audit->>Utilisateur: Oui, voici les informations correspondantes
    Utilisateur->>Simulateur DPE-Audit: Quels sont les résultats de cet audit énergétique ?
    Simulateur DPE-Audit->>Utilisateur: Voici les resultats calculés sur la base des données disponibles
    Utilisateur->>Simulateur DPE-Audit: Quels seraient les résultats de cet audit énergétique avec ce scénario de travaux ?
    Simulateur DPE-Audit->>Utilisateur: Voici les resultats calculés après application du scénario de travaux
```