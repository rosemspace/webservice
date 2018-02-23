<script>
    export default {
        name: 'RosemMotionVisualiser',

        render () {
            return this.$scopedSlots.default({
                draw: this.draw,
                clear: this.clear
            });
        },

        props: {
            canvasId: {
                type: String | Number,
                required: true,
            },
            rotate: {
                type: Boolean,
                default: false,
            },
            swap: {
                type: Boolean,
                default: true
            },
            boundaryOffsetX: {
                type: Number,
                default: 50
            },
            boundaryOffsetY: {
                type: Number,
                default: 50
            },
            axisThickness: {
                type: Number,
                default: 1
            },
            xAxisColor: {
                type: String,
                // default: '#fea795',
                // default: '#486887',
                default: '#ddd'
            },
            yAxisColor: {
                type: String,
                // default: '#fecb8d',
                default: '#ddd',
            },
            gridThickness: {
                type: Number,
                default: 3,
            },
            gridColor: {
                type: String,
                default: '#ddd',
            },
            curveThickness: {
                type: Number,
                default: 3,
            },
            curveColor: {
                type: String,
                // default: '#4fc08d',
                default: '#99b2c9',
            }
        },

        computed: {
            gridCellWidth () {
                return this.width / 10;
            },
            gridCellHeight () {
                return this.height / 10;
            }
        },

        methods: {
            clear () {
                this.context.beginPath();
                this.flush();
                this.drawCenter();
                this.drawAxes(this.boundaryOffsetX, this.boundaryOffsetY);
            },

            flush () {
                this.context.clearRect(0, 0, this.width, this.height);
                this.context.lineWidth = this.gridThickness;
                this.context.strokeStyle = this.gridColor;
                this.context.strokeRect(
                    this.boundaryOffsetX,
                    this.boundaryOffsetY,
                    this.drawAreaWidth,
                    this.drawAreaHeight
                );
                this.context.lineWidth = this.curveThickness;
                this.context.strokeStyle = this.curveColor;
            },

            drawAxes (x, y) {
                this.context.fillStyle = this.xAxisColor;
                this.context.fillRect(x, this.gridCellWidth, this.axisThickness, this.height - this.gridCellWidth * 2);
                this.context.fillStyle = this.yAxisColor;
                this.context.fillRect(this.gridCellHeight, y, this.width - this.gridCellHeight * 2, this.axisThickness);
            },

            drawCenter () {
                this.context.fillStyle = this.gridColor;
                this.context.fillRect(this.width / 2, this.width / 2 - this.gridCellWidth / 2, 1, this.gridCellWidth);
                this.context.fillRect(this.height / 2 - this.gridCellHeight / 2, this.height / 2, this.gridCellHeight,
                    1);
            },

            draw (x, y) {
                let X = this.boundaryOffsetX + this.drawAreaWidth * x,
                    Y = this.boundaryOffsetY + this.drawAreaHeight * y;
                this.flush();
                this.drawCenter();
                this.drawAxes(X, Y);
                this.context.lineTo(X, Y);
                this.context.stroke();
                this.context.moveTo(X, Y);
            }
        },

        mounted () {
            this.$nextTick(() => {
                const canvas = document.getElementById(this.canvasId);
                this.context = canvas.getContext('2d');
                this.width = canvas.width;
                this.height = canvas.height;
                this.drawAreaWidth = this.width - 2 * this.boundaryOffsetX;
                this.drawAreaHeight = this.height - 2 * this.boundaryOffsetY;
                this.clear();
            });
        }
    };
</script>

<style lang="postcss">
    canvas {
        position: fixed;
        right: 0;
        bottom: 0;
        background: white;
        border-top-left-radius: 2px;
        box-shadow: 0 0 10px;
    }
</style>
