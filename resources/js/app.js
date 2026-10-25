import { createIcons, icons } from 'lucide';

window.createLucideIcons = () => {
    createIcons({ icons });
};

document.addEventListener('DOMContentLoaded', () => {
    window.createLucideIcons();
});
