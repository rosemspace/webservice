<script>
    export default {
        name: 'RosemCarousel',

        render() {
            return this.$scopedSlots.default({
                // tapeBindings: {
                    currentSlideIndex: this.currentSlideIndex,
                // },
                nextHandlers: {
                    click: (event) => {
                        event.preventDefault();

                        if (this.infinite) {
                            // TODO
                        } else {
                            if (this.currentSlideIndex < this.range - 1) {
                                this.currentSlideIndex += this.step;
                                this.$emit('next');
                            }
                        }
                    }
                },
                previousHandlers: {
                    click: (event) => {
                        event.preventDefault();

                        if (this.infinite) {
                            // TODO
                        } else {
                            if (this.currentSlideIndex > 0) {
                                this.currentSlideIndex -= this.step;
                                this.$emit('previous');
                            }
                        }
                    }
                },
                paginationHandlers: {},
            })
        },

        props: {
            range: {
                type: Number,
                default: 1,
            },
            direction: {
                type: String,
                default: 'ltr',
                validator: value => value === 'ltr' || value === 'rtl',
            },
            startSlideIndex: {
                type: Number,
                default: 0,
            },
            step: {
                type: Number,
                default: 1,
                validator: value => value > 0,
            },
            infinite: {
                type: Boolean,
                default: false,
            },
        },

        data() {
            return {
                currentSlideIndex: this.startSlideIndex,
            }
        },
    }
</script>
