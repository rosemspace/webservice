import { easeOutElastic } from './easing';

export default {
    name: 'VueMotion',

    render(h) {
        let defaultScopedSlot = this.$scopedSlots.default({
            playing: this.playing,
            value: this.motionValue,
        });

        return this.virtual ? defaultScopedSlot[0] : h(this.tag, [defaultScopedSlot]);
    },

    props: {
        tag: {
            type: String,
            default: 'div',
        },

        virtual: {
            type: Boolean,
            default: true,
        },

        value: {
            type: Number | String,
            default: 0,
            validator: v => v === v && Object.prototype.toString.call(v) === '[object Number]' || v === 'auto',
        },

        delay: {
            type: Number | Object,
            default: 300
        },

        duration: {
            type: Number | Object,
            default: 300
        },

        precision: {
            type: Number,
            default: Infinity
        },

        easing: {
            type: Function,
            default: easeOutElastic,
//                default: easeOutExpo,
        },

        reverse: {
            type: Boolean,
            default: false,
        },

        params: {
            type: Object
        },

        process: {
            type: Function,
            default() {}
        },
    },

    watch: {
        value(newValue, oldValue) {
            if (this.value !== this.startValue + this.intervalValue) {
                this.reverse
                    ? this.intervalValue = oldValue -
                    (this.startValue = this.playing ? this.motionValue : newValue)
                    : this.intervalValue = newValue -
                    (this.startValue = this.playing ? this.motionValue : oldValue);
                this.play();
            }
        }
    },

    computed: {
        factor({ precision }) {
            return Math.pow(10, precision);
        },
        approximate({ precision, factor }) {
            return Number.isFinite(precision)
                ? value => Math.round(value * factor) / factor
                : value => value;
        },
        calculatedValue() {
            const styles = window.getComputedStyle(this.$el);

            return this.value === 'auto'
                ? Math.ceil(this.$el.offsetHeight + parseFloat(styles.marginTop) + parseFloat(styles.marginBottom))
                : this.value;
        },
        calculateDelay() {
            return Object(this.delay) === this.delay ? this.delay : {};
        },
        calculateDuration() {},
    },

    data() {
        return {
            startValue: this.value,
            intervalValue: this.value,
            motionValue: this.value,
            animationId: null,
            startTime: null,
            timePassed: null,
            progress: 0,
            playing: false,
        }
    },

    methods: {
        play({reverse = false} = {}) {
            this.playing = true;
            cancelAnimationFrame(this.animationId);
            this.animationId = requestAnimationFrame((time) => {
                this.startTime = time;
                this.$emit('begin', time);
                this.nextFrame(time);
            });
        },

        stop() {
            cancelAnimationFrame(this.animationId);
            this.playing = false;
            this.motionValue = this.startValue;
        },

        nextFrame(time) {
            this.timePassed = time - this.startTime;

            if (this.timePassed < this.duration) {
                this.animationId = requestAnimationFrame(this.nextFrame)
            } else {
                this.timePassed = this.duration;
                this.$nextTick(() => {
                    this.playing = false;
                    this.$emit('complete', time);
                });

            }

            const timeFraction = this.timePassed / this.duration,
                deformation = this.easing(timeFraction, this.params);
            this.motionValue = this.approximate(this.startValue + this.intervalValue * deformation);
            this.progress = timeFraction * 100;
            this.process(timeFraction, deformation);
        },
    },

    created() {

    }
}
