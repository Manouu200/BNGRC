# 🎨 Guide du Système de Design CSS Professionnel

## 📋 Structure des Fichiers CSS

```
public/assets/css/
├── main.css              # 📌 Fichier principal (importe tous les autres)
├── theme.css             # 🎨 Couleurs, variables et animations globales
├── layout.css            # 📐 Structure page, sidebar, header, footer
├── forms.css             # 📝 Styles pour formulaires et inputs
├── buttons.css           # 🔘 Tous les styles de boutons
├── tables.css            # 📊 Styles pour tableaux et data
├── home.css              # 🏠 Styles spécifiques à la page d'accueil
└── dashboard.css         # 📈 Styles spécifiques au tableau de bord
```

## 🚀 Utilisation Rapide

### Option 1 : Inclure tous les CSS (recommandé)
```html
<head>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
```

### Option 2 : Inclure les CSS individuellement
```html
<head>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/layout.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <link rel="stylesheet" href="/assets/css/buttons.css">
    <link rel="stylesheet" href="/assets/css/tables.css">
</head>
```

## 🎨 Variables CSS Disponibles

### Couleurs
```css
--primary-color: #2563eb          /* Bleu principal */
--primary-dark: #1e40af           /* Bleu foncé */
--primary-light: #3b82f6          /* Bleu clair */
--secondary-color: #64748b
--accent-color: #06b6d4           /* Cyan */
--success-color: #10b981          /* Vert */
--warning-color: #f59e0b          /* Orange */
--danger-color: #ef4444           /* Rouge */
--info-color: #0ea5e9             /* Bleu ciel */
```

### Espacements (Margins/Paddings)
```css
--spacing-xs: 0.25rem
--spacing-sm: 0.5rem
--spacing-base: 1rem
--spacing-md: 1.25rem
--spacing-lg: 1.5rem
--spacing-xl: 2rem
--spacing-2xl: 2.5rem
```

### Coins arrondis (Border Radius)
```css
--radius-sm: 0.375rem
--radius-base: 0.5rem
--radius-md: 0.75rem
--radius-lg: 1rem
--radius-xl: 1.5rem
```

### Ombres (Box Shadow)
```css
--shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
--shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
--shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
--shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
```

### Transitions
```css
--transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
--transition-base: 250ms cubic-bezier(0.4, 0, 0.2, 1);
--transition-slow: 350ms cubic-bezier(0.4, 0, 0.2, 1);
```

## 🧬 Composants Disponibles

### Buttons
```html
<!-- Variants -->
<button class="btn btn-primary">Primary</button>
<button class="btn btn-secondary">Secondary</button>
<button class="btn btn-success">Success</button>
<button class="btn btn-danger">Danger</button>
<button class="btn btn-warning">Warning</button>
<button class="btn btn-info">Info</button>

<!-- Outline -->
<button class="btn btn-outline-primary">Outline Primary</button>

<!-- Sizes -->
<button class="btn btn-sm btn-primary">Small</button>
<button class="btn btn-primary">Normal</button>
<button class="btn btn-lg btn-primary">Large</button>

<!-- Block -->
<button class="btn btn-block btn-primary">Full Width</button>
```

### Forms
```html
<div class="form-group">
    <label class="form-label required">Nom</label>
    <input type="text" class="form-control" placeholder="Entrez le nom">
    <span class="form-help">Texte d'aide optionnel</span>
</div>

<div class="form-group">
    <label class="form-label">Sélection</label>
    <select class="form-select">
        <option>Option 1</option>
        <option>Option 2</option>
    </select>
</div>

<div class="form-check">
    <input type="checkbox" class="form-check-input" id="check1">
    <label class="form-check-label" for="check1">Option</label>
</div>
```

### Cards
```html
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Titre de la Card</h3>
    </div>
    <div class="card-body">
        <!-- Contenu -->
    </div>
</div>
```

### Tables
```html
<div class="table-wrapper">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Colonne 1</th>
                <th>Colonne 2</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Donnée 1</td>
                <td>Donnée 2</td>
            </tr>
        </tbody>
    </table>
</div>
```

### Badges
```html
<span class="badge badge-primary">Primary</span>
<span class="badge badge-success">Success</span>
<span class="badge badge-warning">Warning</span>
<span class="badge badge-danger">Danger</span>
```

### Alerts
```html
<div class="alert alert-success">✓ Message de succès</div>
<div class="alert alert-warning">⚠ Message d'avertissement</div>
<div class="alert alert-danger">✕ Message d'erreur</div>
<div class="alert alert-info">ℹ Message d'information</div>
```

## 🎯 Classes Utilitaires

### Texte
```html
<p class="text-muted">Texte grisé</p>
<p class="text-secondary">Texte secondaire</p>
<p class="text-success">Texte de succès</p>
<p class="text-danger">Texte d'erreur</p>
```

### Fond
```html
<div class="bg-light">Fond clair</div>
<div class="bg-lighter">Fond plus clair</div>
```

### Animations
```html
<div class="fadeIn">Apparition en fondu</div>
<div class="slideInLeft">Arrivée par la gauche</div>
<div class="slideInUp">Arrivée par le bas</div>
```

## 📱 Responsive Design

Le design est complètement responsive :
- **Desktop** : Version complète avec sidebar fixe
- **Tablet** : Adaptation du layout
- **Mobile** : Sidebar réduite, layout en colonne

```css
@media (max-width: 768px) {
    /* Styles mobiles */
}
```

## 🌓 Support Mode Sombre

Le CSS supporte automatiquement les préférences du système :
```css
@media (prefers-color-scheme: dark) {
    /* Styles pour mode sombre */
}
```

## ♿ Accessibilité

Le design respecte les standards WCAG :
- Contraste des couleurs suffisant
- Focus visible pour la navigation au clavier
- Labels associés aux inputs
- Sémantique HTML correcte

## 🔧 Personnalisation

Pour modifier les couleurs ou variables, editez simplement `theme.css` :

```css
:root {
    --primary-color: #YOUR_COLOR;
    --secondary-color: #YOUR_COLOR;
    /* ... autres variables */
}
```

## 📊 Pratiques Recommandées

1. **Utilisez les variables CSS** au lieu de couleurs en dur
2. **Utilisez les espaces** définis plutôt que de créer de nouveaux
3. **Chaînez les classes** pour les états (btn btn-primary)
4. **Testez la responsivité** sur tous les appareils
5. **Validez le contraste** pour l'accessibilité

## 🐛 Support Navigateurs

- Chrome/Edge : ✅ Tous les version récentes
- Firefox : ✅ Tous les version récentes
- Safari : ✅ Tous les version récentes
- IE11 : ⚠️ Support limité (variables CSS non supportées)

## 📚 Ressources

- [CSS Custom Properties (Variables)](https://developer.mozilla.org/en-US/docs/Web/CSS/--*)
- [Flexbox Guide](https://css-tricks.com/snippets/css/a-guide-to-flexbox/)
- [Grid Guide](https://css-tricks.com/snippets/css/complete-guide-grid/)

---

**Créé**: 16 février 2026  
**Version**: 1.0.0  
**Auteur**: Maude
