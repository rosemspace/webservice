<template>
    <main :class="$options.name">
        <!--<div :class="`${$options.name}-bg-image`"></div>-->
        <the-header />
        <transition :name="`${$options.name}-fade`" :duration="400">
            <keep-alive>
                <router-view class="page-content" />
            </keep-alive>
        </transition>
        <!--<footer v-once>The footer</footer>-->
    </main>
</template>

<script>
import TheHeader from "./../partials/TheHeader";

export default {
  name: "TheAdminLayout",

  components: {
    TheHeader
  }
};
</script>

<style lang="postcss" scoped>
@import "../../../../rosem-css/style.pcss";

.TheAdminLayout {
  display: grid;
  grid-template:
    "header header header" auto "navigation content sidebar" 1fr "footer footer footer" auto /
    1fr 2fr 1fr;
  min-height: 100%;
  /*background: linear-gradient(8deg, rgba(120, 158, 189, 0.4), rgba(222, 228, 236, 0.9));*/
  /*background: linear-gradient(8deg, rgb(215, 206, 201), rgba(120, 158, 189, 0.4));*/
  background: linear-gradient(
    8deg,
    rgba(120, 158, 189, 0.4),
    rgba(216, 225, 236, 0.2)
  );

  & > .TheHeader {
    grid-area: header;
  }

  & >>> .page-content {
    display: contents;
    contain: paint;
    overflow: hidden;
    z-index: 0;

    & > nav {
      display: flex;
      grid-area: navigation;
      color: #354b56;
      /*background: linear-gradient(8deg, #2e5777, #1d3151);*/
      /*background: linear-gradient(8deg, #054e88, #1d3151);*/
      /*background: linear-gradient(8deg, rgba(5, 78, 136, 0.56), rgba(29, 49, 81, 0.28));*/
      /*background: linear-gradient(8deg, rgb(120, 158, 189), rgba(222, 228, 236, 0.7));*/

      & > aside {
        min-width: 5.6rem;
        background: rgba(102, 128, 158, 0.25);
      }

      & > section {
        padding: 2rem;
      }
    }

    & > section {
      grid-area: content;

      & > article {
        margin: 2rem;
        border-radius: 0.4rem;
        background: white;
        box-shadow: 0 2px 2px #98afc0;

        & > header {
          padding: var(--space-small);
          border-bottom: dashed 1px #dae9f5;

          & > :--heading {
            font-weight: 300;
            margin: var(--space-small);
            color: #aab5c2;
          }
        }

        & p {
          line-height: 1.5;
        }

        & .short-description {
          padding: 2rem;
        }
      }
    }

    & > aside {
      grid-area: sidebar;
      padding: 2rem;
    }
  }

  & > footer {
    grid-area: footer;
    padding: 2rem;
    color: white;
    background: #1d3151;
  }
}

.TheAdminLayout-bg-image {
  background: #f1f1f1 url("~@rosem/admin/assets/bg-clouds.jpg");
  background-size: cover;
  filter: blur(30px);
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: -1;
}
</style>

<style lang="postcss">
.TheAdminLayout-fade-enter-active,
.TheAdminLayout-fade-leave-active {
  & > nav,
  & > section > *,
  & > aside {
    transition-property: opacity;
    transition-duration: 400ms;
  }

  & > section {
    transition-property: transform;
    transition-duration: 400ms;
  }
}
.TheAdminLayout-fade-enter,
.TheAdminLayout-fade-leave-to {
  position: absolute;

  & > nav,
  & > section > *,
  & > aside {
    opacity: 0;
  }
}
.TheAdminLayout-fade-enter {
  & > section {
    transform: translateY(10px);
  }
}
.TheAdminLayout-fade-leave-to {
  & > section {
    transform: translateY(-10px);
  }
}
</style>
