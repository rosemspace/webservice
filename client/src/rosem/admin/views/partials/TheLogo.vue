<template>
    <div :class="$options.name">
        <router-link :to="{name: 'admin'}">
            <img src="require('@rosem/admin/asstes/logo.png')" alt="The logo" />
            <!--<span>Rosem</span>-->
        </router-link>
    </div>
</template>

<script>
export default {
  name: "TheLogo"
};
</script>

<style lang="postcss">
.TheLogo {
  filter: hue-rotate(10deg);

  & > a {
    clip-path: polygon(0 0, 0 100%, 83% 96%, 96% 83%, 100% 0);
    background: #25547b;
    transform: scale(1.5);
    transition: background-color 0.4s;

    &:hover {
      background: #bee6fd;
    }

    & > img {
      width: 5rem;
      height: 5rem;
      margin-top: 0.6rem;
      margin-left: 0.6rem;
      transform: scale(0.65);
    }
  }
}
</style>
