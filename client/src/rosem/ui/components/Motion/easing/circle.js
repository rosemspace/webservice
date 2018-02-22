export function circleIn (timeFraction) {
    return 1 - Math.sqrt(1 - timeFraction * timeFraction);
}

export function circleOut (timeFraction) {
    return Math.sqrt(1 - --timeFraction * timeFraction);
}

export function circleInOut (timeFraction) {
    return (
        (timeFraction *= 2) <= 1
            ? circleIn(timeFraction)
            : Math.sqrt(1 - (timeFraction -= 2) * timeFraction) + 1
    ) / 2;
}
