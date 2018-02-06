<template>
    <div :id="`popup-${name}`" :class="$options.name">
        <slot :open="open" />
    </div>
</template>

<script>
    export default {
        name: 'RosemPopup',

//        render(h) {
//            return h(this.tag, {
//                attrs: {
//                    id: `popup-${this.name}`,
//                    class: this.$options.name,
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
                required: true,
            }
        },

        data() {
            return {
                open: false,
            }
        },

        beforeCreate() {
            this.$_rosem_popup.init(this.$root);
        },

        mounted() {
            this.$nextTick(() => {
                this.$_rosem_popup.add(this.name, {
                    popperElement: this.$el,
                    closeOnControlClick: true,
                    closeOnSelfClick: true,
                    closeOnClickOutside: true,
                    closeOnPressEscape: true,
                });
                this.$_rosem_popup.onToggle(this.name, event => {
                    this.open = event.open;
                });
            });
        },

        destroyed() {
            this.$_rosem_popup.remove(this.name);
        },
    }
</script>

<style lang="postcss">
    .RosemPopup {
        margin: 1rem;

        & [x-arrow] {
            width: 0;
            height: 0;
            border-style: solid;
            position: absolute;
            margin: 5px;
            color: white;
        }
    }

    .RosemPopup[x-placement^="top"] {
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

    .RosemPopup[x-placement^="right"]{
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

    .RosemPopup[x-placement^="bottom"] {
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

    .RosemPopup[x-placement^="left"] {
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
