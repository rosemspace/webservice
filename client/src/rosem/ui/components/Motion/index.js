import { easeInElastic, easeOutElastic, easeOutExpo, easeOutBounce } from './easing';
import { circleIn, circleInOut } from './easing/circle';

export default {
    name: 'RosemMotion',

    render() {
        return this.$scopedSlots.default({
            playing: this.playing,
            value: this.motionValue,
        })
    },

    props: {
        tag: {
            type: String,
            default: 'div',
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
            // default: timeFraction => timeFraction,
            // default: circleIn,
            default: circleInOut,
            // default: easeOutBounce
            // default: easeInElastic,
            // default: easeOutElastic,
            //    default: easeOutExpo,
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
        factor() {
            return Math.pow(10, this.precision);
        },
        approximate() {
            return Number.isFinite(this.precision)
                ? value => Math.round(value * this.factor) / this.factor
                : value => value;
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
            cancelAnimationFrame(this.animationId); // TODO: add motion reset
            this.animationId = requestAnimationFrame((time) => {
                this.startTime = time;
                this.$emit('motion-start', time);
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
                    this.$emit('motion-end', time);
                });
            }

            const timeFraction = this.timePassed / this.duration,
                deformation = this.easing(timeFraction, this.params);
            this.motionValue = this.approximate(this.startValue + this.intervalValue * deformation);
            this.progress = timeFraction * 100;
            this.process(timeFraction, deformation);
        },
    },
}
