import Poptip from './Poptip';
import Popper from './Popper';
import PoptipManager from './poptipManager';

export default {
    install (Vue, params = {}) {
        const poptipManager = Vue.prototype.$_poptip = new PoptipManager;

        let uuid = 0;

        Vue.prototype.$_uuid = () => {
            Vue.prototype.$_uuid.last = uuid.toString();
            uuid += 1;

            return Vue.prototype.$_uuid.last;
        };

        Vue.component(Poptip.name, Poptip);
        Vue.component(Popper.name, Popper);
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

                poptipManager.add(binding.value, {
                    eventBus: vnode.context.$root,
                    targetElement: el,
                    placement: firstModifier[1] ? firstModifier[0] : '',
                });

                if (! binding.arg || binding.arg === 'click') {
                    el.addEventListener('click', event => {
                        poptipManager.toggle(binding.value);
                    });
                } else if (binding.arg === 'hover') {
                    el.addEventListener('mouseenter', event => {
                        poptipManager.open(binding.value);
                    });
                    el.addEventListener('mouseleave', event => {
                        poptipManager.close(binding.value);
                    });
                } else {
                    throw new Error('modifier of the v-pop directive should be "click" or "hover"');
                }
            },

            unbind: function (target, binding) {
                poptipManager.remove(binding.arg);
            },
        });
    },
}
