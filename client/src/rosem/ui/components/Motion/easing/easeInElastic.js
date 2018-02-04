export default (timeFraction, {amplitude = 1, frequency = 3, decay = 8} = {}) =>
    amplitude * Math.cos(frequency * timeFraction * 2 * Math.PI) / Math.exp(decay * (1 - timeFraction))
