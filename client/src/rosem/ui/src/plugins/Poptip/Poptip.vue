<template>
    <div :id="`popup-${name}`" :class="$options.name">
        <slot name="control" :id="_uid" />
        <div ref="popover">
            <slot :open="open" />
        </div>
    </div>
</template>

<script>
    export default {
        name: 'RosemPoptip',

//        render(h) {
//            return h(this.tag, {
//                attrs: {
//                    id: `popup-${this.name}`,
//                },
//            }, [
//                this.$scopedSlots.default({
//                    open: this.open,
//                })
//            ]);
//        },

        props: {
            tag: {
                type: String,
                default: 'div'
            },

            name: {
                type: String,
                // required: true,
            }
        },

        watch: {},

        computed: {},

        data() {
            return {
                open: false,
                poptip: this.$_poptip,
            }
        },

        methods: {},

        mounted() {
            this.$nextTick(() => {
                const name = this.name || this._uid;

                console.log(this.$refs.popover);

                this.$_poptip.add(name, {
                    eventBus: this.$root,
                    // targetElement: this.$slots.control,
                    popperElement: ! this.name ? this.$refs.popover : this.$el,
                    closeOnControlClick: true,
                    closeOnSelfClick: true,
                    closeOnClickOutside: true,
                    closeOnPressEscape: true,
                });

                this.$root.$on('poptip:open', event => {
                    if (name === event.name) {
                        this.$_poptip.update(name);
                        this.open = true;
                    }
                });

                this.$root.$on('poptip:close', event => {
                    if (name === event.name) {
                        this.open = false;
                    }
                });
            });
        },

        destroyed() {
            this.$_poptip.remove(this.name);
        },
    }
</script>

<style lang="postcss">
    .RosemPoptip {
        & [x-arrow] {
            width: 0;
            height: 0;
            border-style: solid;
            position: absolute;
            margin: 5px;
            color: white;
        }
    }

    .RosemPoptip[x-placement^="top"] {
        & [x-arrow] {
            border-width: 5px 5px 0 5px;
            border-left-color: transparent;
            border-right-color: transparent;
            border-bottom-color: transparent;
            bottom: -5px;
            left: calc(50% - 5px);
            margin-top: 0;
            margin-bottom: 0;
        }
    }

    .RosemPoptip[x-placement^="right"]{
        & [x-arrow] {
            border-width: 5px 5px 5px 0;
            border-left-color: transparent;
            border-top-color: transparent;
            border-bottom-color: transparent;
            left: -5px;
            top: calc(50% - 5px);
            margin-left: 0;
            margin-right: 0;
        }
    }

    .RosemPoptip[x-placement^="bottom"] {
        & [x-arrow] {
            border-width: 0 5px 5px 5px;
            border-left-color: transparent;
            border-right-color: transparent;
            border-top-color: transparent;
            top: -5px;
            left: calc(50% - 5px);
            margin-top: 0;
            margin-bottom: 0;
        }
    }

    .RosemPoptip[x-placement^="left"] {
        & [x-arrow] {
            border-width: 5px 0 5px 5px;
            border-top-color: transparent;
            border-right-color: transparent;
            border-bottom-color: transparent;
            right: -5px;
            top: calc(50% - 5px);
            margin-left: 0;
            margin-right: 0;
        }
    }
</style>
