<template>
    <div>
        <slot :draw="draw" :clear="clear"/>
        <canvas ref="canvas" width="250" height="250"></canvas>
    </div>
</template>

<script>
    export default {
        name: 'RosemMotionVisualiser',

        props: {
            rotate: {
                type: Boolean,
                default: false,
            },
            swap: {
                type: Boolean,
                default: true,
            },
            boundaryOffsetX: {
                type: Number,
                default: 50,
            },
            boundaryOffsetY: {
                type: Number,
                default: 50,
            },
            xAxisColor: {
                type: String,
                default: '#fea795',
            },
            yAxisColor: {
                type: String,
                default: '#fecb8d',
            },
            areaBorderWidth: {
                type: Number,
                default: 2,
            },
            areaBorderColor: {
                type: String,
                default: '#e7e7e7',
            },
            graphLineColor: {
                type: String,
                default: '#4fc08d',
            },
        },

        methods: {
            clear() {
                this.context.beginPath();
                this.flush();
                this.drawAxes(
                    this.boundaryOffsetX + this.drawAreaWidth,
                    this.height - this.boundaryOffsetY - this.drawAreaHeight
                );
            },

            flush() {
                this.context.clearRect(0, 0, this.width, this.height);
                this.context.lineWidth = this.areaBorderWidth;
                this.context.strokeStyle = this.areaBorderColor;
                this.context.strokeRect(
                    this.boundaryOffsetX,
                    this.boundaryOffsetY,
                    this.drawAreaWidth,
                    this.drawAreaHeight
                );
                this.context.lineWidth = 1.5;
                this.context.strokeStyle = this.graphLineColor;
            },

            drawAxes(x, y) {
                this.context.fillStyle = this.xAxisColor;
                this.context.fillRect(x, 25, 1, this.height - 50);
                this.context.fillStyle = this.yAxisColor;
                this.context.fillRect(25, y, this.width - 50, 1);
            },

            draw(x, y) {
                let X = this.boundaryOffsetX + this.drawAreaWidth * x,
                    Y = this.height - this.boundaryOffsetY - this.drawAreaHeight * y,
                    direction = !this.rotate - this.rotate,
                    previousX = X;
                X = this.rotate * this.width + direction * X;
                Y = this.rotate * this.height + direction * Y;
                X = !this.swap * X || Y;
                Y = !this.swap * Y || previousX;
                this.flush();
                this.drawAxes(X, Y);
                this.context.lineTo(X, Y);
                this.context.stroke();
                this.context.moveTo(X, Y);
            },
        },

        mounted() {
            this.$nextTick(() => {
                this.context = this.$refs.canvas.getContext('2d');
                this.width = this.$refs.canvas.width;
                this.height = this.$refs.canvas.height;
                this.drawAreaWidth = this.width - 2 * this.boundaryOffsetX;
                this.drawAreaHeight = this.height - 2 * this.boundaryOffsetY;
                this.clear();
            })
        },
    }
</script>

<style lang="postcss" scoped>
    canvas {
        float: left;
        background: white;
    }
</style>
