import Popup from './Popup';
import PopupTooltip from './PopupTooltip';
import PopupManager from './PopupManager';

export default {
    install (Vue, params = {}) {
        const popupManager = Vue.prototype.$_rosem_popup = new PopupManager;

        Vue.component(Popup.name, Popup);
        Vue.component(PopupTooltip.name, PopupTooltip);
        Vue.directive('pop', {
            bind(el, binding, vnode) {
                if (! binding.value) {
                    throw new Error(
                        'value of the v-pop directive should be provided' +
                        ' as a string name of a bound popup element'
                    );
                }

                el.setAttribute('aria-controls', binding.value);

                const firstModifier = Object.entries(binding.modifiers)[0];

                popupManager.add(binding.value, {
                    targetElement: el,
                    placement: firstModifier[1] ? firstModifier[0] : '',
                });

                if (! binding.arg || binding.arg === 'click') {
                    el.addEventListener('click', event => {
                        popupManager.toggle(binding.value);
                    });
                } else if (binding.arg === 'hover') {
                    el.addEventListener('mouseenter', event => {
                        popupManager.open(binding.value);
                    });
                    el.addEventListener('mouseleave', event => {
                        popupManager.close(binding.value);
                    });
                } else {
                    throw new Error('modifier of the v-pop directive should be "click" or "hover"');
                }
            },

            unbind: function (target, binding) {
                popupManager.remove(binding.arg);
            },
        });
    },
}
