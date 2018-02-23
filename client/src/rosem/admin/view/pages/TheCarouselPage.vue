<template>
    <div :class="$options.name">
        <nav>
            <aside></aside>
            <section>Carousel navigation</section>
        </nav>
        <section>
            <rosem-carousel :range="3">
                <div class="carousel" slot-scope="props">
                    <rosem-motion-visualiser canvas-id="curve">
                    <rosem-motion slot-scope="visualizer"
                                  :process="visualizer.draw"
                                  @motion-start="visualizer.clear"
                                  :value="props.currentSlideIndex"
                                  :duration="2000"
                    >
                        <ul slot-scope="motionProps"
                            :style="`transform: translateX(${-motionProps.value * 100}%)`"
                        >
                            <li>
                                <figure>
                                    <img src="https://www.w3schools.com/howto/img_mountains_wide.jpg">
                                </figure>
                            </li>
                            <li>
                                <figure>
                                    <img src="https://www.w3schools.com/howto/img_nature_wide.jpg">
                                </figure>
                            </li>
                            <li>
                                <figure>
                                    <img src="https://www.w3schools.com/howto/img_fjords_wide.jpg">
                                </figure>
                            </li>
                        </ul>
                    </rosem-motion>
                    </rosem-motion-visualiser>
                    <button v-on="props.previousHandlers">Prev</button>
                    <button v-on="props.nextHandlers">Next</button>
                </div>
            </rosem-carousel>
        </section>
        <aside>Carousel sidebar</aside>
        <canvas id="curve" width="250" height="250"></canvas>
    </div>
</template>

<script>
    export default {
        name: 'TheCarouselPage',
    }
</script>

<style lang="postcss">
    .TheCarouselPage {
        & section {
            padding: 2rem;
        }

        & .carousel {
            contain: paint;

            & ul {
                display: flex;
                max-width: 1000px;
                /*transition: transform .6s;*/
            }
        }
    }
</style>
