<template>
    <header :class="$options.name">
        <the-logo />
        <nav>
            <ul>
                <li>
                    <router-link :to="{name: 'home'}">Home</router-link>
                </li>
                <li>
                    <router-link :to="{name: 'about'}">About</router-link>
                </li>
                <li>
                    <a href="/admin">Admin</a>
                </li>
            </ul>
        </nav>
    </header>
</template>

<script>
import TheLogo from "./TheLogo";

export default {
  name: "TheHeader",

  components: {
    TheLogo
  }
};
</script>

<style lang="postcss">
.TheHeader {
  display: flex;
  color: white;
  background: #3071a7;

  & ul {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    text-transform: uppercase;
    margin-left: 1rem;

    & > li > a {
      padding: 2rem 1rem;
      border-bottom: 0 solid #3071a7;
      color: #d2efff;
      transition: color 0.2s, border-bottom 0.2s, padding-bottom 0.2s;

      &:hover,
      &.router-link-exact-active {
        color: white;
        padding-bottom: 1.8rem;
        border-bottom-width: 0.2rem;
        border-bottom-color: #8ccaff;
      }
    }
  }
}
</style>
