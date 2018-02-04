import easeInElastic from './easeInElastic';

export default (timeFraction, {amplitude = 1, frequency = 3, decay = 8} = {}) =>
    1 - easeInElastic(1 - timeFraction, {amplitude, frequency, decay})
