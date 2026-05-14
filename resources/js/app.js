import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('toast', {
    visible: false,
    message: '',
    timer: null,
    type: 'success',
    show(msg, type = 'success') {
        this.message = msg;
        this.type = type;
        this.visible = true;
        clearTimeout(this.timer);
        this.timer = setTimeout(() => this.visible = false, 3000);
    }
});

Alpine.start();
