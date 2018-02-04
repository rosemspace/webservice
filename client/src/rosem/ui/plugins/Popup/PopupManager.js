import Popper from 'popper.js';

export default class PopupManager {
    initialized = false;
    bindings = {};
    activeList = [];
    _id = 0;
    id;

    init () {
        if (!this.initialized) {
            document.addEventListener('click', this.eventListener = this._onDocumentClick.bind(this));
            this.initialized = true;
        }
    }

    nextId() {
        return this.id = (++this._id).toString();
    }

    destroy () {
        if (this.initialized) {
            document.removeEventListener('click', this.eventListener);
            this.initialized = false;
        }
    }

    _onDocumentClick (event) {
        for (let i = this.activeList.length; --i >= 0;) {
            const activePopperName = this.activeList[i],
                binding = this.bindings[activePopperName];

            if (binding.closeOnClickOutside &&
                (!binding.popperElement.contains(event.target) || binding.closeOnSelfClick) &&
                !binding.targetElement.contains(event.target)
            ) {
                this.close(activePopperName);
            }
        }
    };

    add (name, newParams) {
        let params = this.bindings[name];

        this.bindings[name] = params ? Object.assign(params, newParams) : params = newParams;

        console.log(newParams);

        if (params.targetElement && params.popperElement) {
            params.eventBus = newParams.eventBus;
            params.popper = new Popper(params.targetElement, params.popperElement, {
                // arrowElement: binding.pointerElement,
                originalPlacement: 'bottom',
                placement: params.placement,
                // flipped: false,
                // removeOnDestroy: true,
                modifiers: {
                    // keepTogether: { enabled: true }
                    preventOverflow: {
                        padding: 0
                        // disabled: true,
                        // boundariesElement: document.body,
                    }
                }
            });
            // binding.popper.enableEventListeners();
            this.init();
        }
    }

    remove (name) {
        if (this.bindings[name]) {
            this.bindings[name].popper.destroy();
            delete this.bindings[name];

            if (!Object.keys(this.bindings).length) {
                this.destroy();
            }
        }
    }

    update (name) {
        this.bindings[name].popper.scheduleUpdate();
    }

    _forceOpen (name) {
        this.activeList.push(name);
        let binding = this.bindings[name];
        binding.open = true;
        binding.eventBus.$emit('poptip:open', {name});
    }

    open (name) {
        if (!this.bindings[name].open) {
            this._forceOpen(name);
        }
    }

    _forceClose (name) {
        this.activeList.splice(this.activeList.indexOf(name), 1);
        let binding = this.bindings[name];
        binding.open = false;
        binding.eventBus.$emit('poptip:close', {name});
    }

    close (name) {
        if (this.bindings[name].open) {
            this._forceClose(name);
        }
    }

    toggle (name) {
        const binding = this.bindings[name];

        binding.open && binding.closeOnControlClick
            ? this._forceClose(name)
            : this._forceOpen(name);
    }
}
