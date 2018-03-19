<template>
    <div :class="$options.name">
        <nav>
            <aside></aside>
            <section>Carousel navigation</section>
        </nav>
        <section>
            <rosem-carousel :range="6">
                <div class="carousel" slot-scope="carouselProps">
                    <h4>{{ carouselProps.currentSlideIndex }}</h4>
                    <transition-group tag="ul"
                                      :name="`slide-${carouselProps.currentSlideIndex - carouselProps.previousSlideIndex > 0 ? 'next' : 'previous'}`"
                                      @enter-cancelled="enterCancelled"
                                      @leave-cancelled="leaveCancelled"
                    >
                        <li v-for="(slide, index) in slides"
                            v-show="index === carouselProps.currentSlideIndex"
                            :key="index"
                        >
                            <figure>
                                <img :src="slide.imgSrc">
                                <figcaption :style="{color: slide.titleColor}">{{ slide.title }}</figcaption>
                            </figure>
                        </li>
                    </transition-group>
                    <div class="navigation">
                        <span v-on="carouselProps.previousHandlers"><svg><use xlink:href="/images/arrows.svg#left"/></svg></span>
                        <span v-on="carouselProps.nextHandlers"><svg><use xlink:href="/images/arrows.svg#right"/></svg></span>
                    </div>
                    <div class="pagination">
                        <div v-for="(slide, index) in slides"
                             :key="index"
                             :class="{active: index === carouselProps.currentSlideIndex}"
                        ></div>
                    </div>
                </div>
            </rosem-carousel>
            <rosem-carousel :range="6">
                <div class="carousel" slot-scope="carouselProps">
                    <rosem-motion-visualiser canvas-id="curve">
                    <rosem-motion slot-scope="visualizer"
                                  :process="(timePassed, deformation) => {visualizer.draw(timePassed, deformation); updateInfo(timePassed * 2000, deformation * 100)}"
                                  @start="visualizer.clear"
                                  :value="carouselProps.currentSlideIndex"
                                  :duration="1000"
                    >
                        <ul slot-scope="motionProps"
                            :style="`width: ${6 * 100}%; transform: translateX(${-motionProps.value * 100 / 6}%)`"
                        >
                            <li v-for="(slide, index) in slides" :key="index"
                            >
                                <!--:aria-hidden="String(index !== carouselProps.currentSlideIndex)"-->
                                <!--:style="-->
<!--index === carouselProps.previousSlideIndex && motionProps.running-->
<!--? `display: block; transform: translateX(${-(motionProps.oscillation) * 100}%)` :-->
<!--index === carouselProps.currentSlideIndex && motionProps.running-->
<!--? `position: absolute; transform: translateX(${(1 - motionProps.oscillation) * 100}%)` : ''"-->
                                <figure>
                                    <img :src="slide.imgSrc">
                                    <figcaption :style="{color: slide.titleColor}">{{ slide.title }}</figcaption>
                                </figure>
                            </li>
                        </ul>
                    </rosem-motion>
                    </rosem-motion-visualiser>
                    <div class="navigation">
                        <span v-on="carouselProps.previousHandlers"><svg><use xlink:href="/images/arrows.svg#left"/></svg></span>
                        <span v-on="carouselProps.nextHandlers"><svg><use xlink:href="/images/arrows.svg#right"/></svg></span>
                    </div>
                    <div class="pagination">
                        <div v-for="(slide, index) in slides" :key="index"
                             :class="{active: index === carouselProps.currentSlideIndex}"
                        ></div>
                    </div>
                </div>
            </rosem-carousel>
        </section>
        <aside>
            <h4>Motion curve visualisation</h4>
            <canvas id="curve" width="700" height="700"></canvas>
            <h4>Motion information</h4>
            <dl>
                <dt>Time passed, ms</dt>
                <dd>{{ info.timePassed }}</dd>
                <dt>Deformation, %</dt>
                <dd>{{ info.deformation }}</dd>
            </dl>
        </aside>
    </div>
</template>

<script>
export default {
  name: "TheCarouselPage",

  data() {
    return {
      info: {
        timePassed: 0,
        deformation: 0
      },
      slides: [
        {
          title: "Woods",
          imgSrc: "https://www.w3schools.com/howto/img_woods.jpg"
        },
        {
          title: "Mountains",
          titleColor: "rgba(143, 152, 160, 0.7)",
          imgSrc: "https://www.w3schools.com/howto/img_mountains.jpg"
        },
        {
          title: "Snow",
          imgSrc: "https://www.w3schools.com/howto/img_snow.jpg"
        },
        {
          title: "Fjords",
          imgSrc: "https://www.w3schools.com/howto/img_fjords.jpg"
        },
        {
          title: "Lights",
          imgSrc: "https://www.w3schools.com/howto/img_lights.jpg"
        },
        {
          title: "Nature",
          titleColor: "rgba(234, 210, 168, 0.9)",
          imgSrc: "https://www.w3schools.com/howto/img_nature.jpg"
        }
      ]
    };
  },

  methods: {
    enterCancelled() {
      console.log("enterCancelled");
    },
    leaveCancelled() {
      console.log("leaveCancelled");
    },
    updateInfo(timePassed, deformation) {
      this.info.timePassed = Math.floor(timePassed);
      this.info.deformation = Math.floor(deformation);
    }
  }
};
</script>

<style lang="postcss" scoped>
.carousel-fade-enter-active,
.carousel-fade-leave-active {
  transition-property: opacity;
  transition-duration: 2s;
  margin-right: -100%;
}

.carousel-fade-enter,
.carousel-fade-leave-to {
  opacity: 0;
}

.slide-next-enter-active,
.slide-next-leave-active,
.slide-previous-enter-active,
.slide-previous-leave-active {
  transition-property: transform;
  transition-duration: 2s;
  margin-right: -100%;
}

.slide-next-enter {
  transform: translateX(100%);
}

.slide-next-leave-to {
  transform: translateX(-100%);
}

.slide-previous-enter {
  transform: translateX(-100%);
}

.slide-previous-leave-to {
  transform: translateX(100%);
}

aside {
  & h4 {
    position: relative;
    font-size: initial;
    font-weight: bold;
    margin-bottom: 1rem;
  }

  & dl {
    display: flex;
    flex-wrap: wrap;

    & > dt,
    & > dd {
      width: 50%;
    }

    & > dt {
      font-size: 1.4rem;
      margin-bottom: 0.5rem;
    }

    & > dd {
      font-size: 2.4rem;
      order: 1;
    }
  }
}

.carousel {
  & .navigation span {
    fill: rgba(255, 255, 255, 0.6);
    position: absolute;
    top: 50%;
    background: rgba(0, 0, 0, 0.4);
    transition: transform 0.2s, background-color 0.2s;

    &:hover {
      cursor: pointer;
      background: rgba(0, 0, 0, 0.6);
    }

    & > svg {
      width: 40px;
      height: 40px;
      transform: scale(0.5);
    }

    &:first-child {
      right: 0;
      padding: 0 0 1rem;
      border-bottom-left-radius: 0.5rem;
      transform: translate(100%, 0);
    }

    &:last-child {
      right: 0;
      padding: 1rem 0 0;
      border-top-left-radius: 0.5rem;
      transform: translate(100%, -100%);
    }
  }

  &:hover {
    & .navigation span {
      &:first-child {
        transform: translate(10%, 0);
      }

      &:last-child {
        transform: translate(10%, -100%);
      }
    }
  }

  & .pagination {
    display: flex;
    justify-content: space-around;
    margin-top: 1rem;

    & > :first-child {
      margin-left: auto;
    }

    & > :last-child {
      margin-right: auto;
    }

    & > div {
      width: 1rem;
      height: 1rem;
      margin: 1rem 0.5rem;
      border-radius: 50%;
      background-color: #c6ced4;

      &.active {
        background-color: #7c8896;
      }
    }
  }
}

.TheCarouselPage {
  & section {
    padding: 2rem;
  }

  & .carousel {
    contain: paint;
    padding: 1rem 0;
    border-radius: 0.4rem;
    background: white;
    box-shadow: 0 2px 2px #b1bbc1;

    & ul {
      display: flex;
      overflow: hidden;
      margin: 0;
      max-height: 400px;
      /*transition: transform .6s;*/

      & > li {
        width: 100%;
        background: rebeccapurple;

        &[aria-hidden="true"] {
          display: none;
        }

        & > figure {
          height: 100%;

          & > img {
            width: 100%;
            height: 100%;
            object-fit: cover;
          }

          & > figcaption {
            position: absolute;
            bottom: 0;
            /*left: 0;*/
            font-size: 32px;
            padding: 2rem;
            /*color: rgba(214, 224, 234, 0.7);*/
            color: rgb(161, 200, 194);
          }
        }
      }
    }
  }

  & canvas {
    margin: -238px -247px;
  }
}
</style>
