<template>
    <main>
        <the-header />
        <nav>
            <aside></aside>
            <section>The navigation</section>
        </nav>
        <router-view/>
        <footer v-once>The footer</footer>
    </main>
</template>

<script>
import TheHeader from "./../partials/TheHeader";

export default {
  components: {
    TheHeader
  }
};
</script>

<style lang="postcss">
main {
  display: grid;
  grid-template:
    "header header header" auto "navigation content sidebar" 1fr "footer footer footer" auto /
    1fr minmax(auto, 1000px) 1fr;
  min-height: 100%;

  color: #25304c;
  background: #f1f1f1;

  & > .TheHeader {
    grid-area: header;
  }

  & > nav {
    display: flex;
    grid-area: navigation;
    color: #a0ddff;
    background: #374250;

    & > aside {
      width: 5.6rem;
      background: #515a73;
    }

    & > section {
      padding: 2rem;
    }
  }
  & > section,
  & > div > section {
    grid-area: content;
    padding: 2rem;
  }
  & > aside,
  & > div > aside {
    grid-area: sidebar;
    padding: 2rem;
  }
  & > footer {
    grid-area: footer;
  }

  & > footer {
    padding: 2rem;
    color: white;
    background: #1d3151;
  }
}
</style>
