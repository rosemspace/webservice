export default (timeFraction) =>
    timeFraction < 1 / 2.75
        ? 7.5625 * timeFraction * timeFraction
        : timeFraction < 2 / 2.75
            ? 7.5625 * (timeFraction -= 1.5 / 2.75) * timeFraction + .75
            : timeFraction < 2.5 / 2.75
                ? 7.5625 * (timeFraction -= 2.25 / 2.75) * timeFraction + .9375
                : 7.5625 * (timeFraction -= 2.625 / 2.75) * timeFraction + .984375
