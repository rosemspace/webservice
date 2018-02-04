<template>
    <router-link :class="$options.name" :to="{name: 'home'}">
        <!--<img src="../../images/logo.png" alt="The logo" />-->
        <!--<span>Rosem</span>-->
    </router-link>
</template>

<script>
    export default {
        name: 'TheLogo',
    }
</script>

<style lang="postcss">
    .TheLogo {
        filter: hue-rotate(10deg);
        background: #25547b;
        transition: background-color .4s;

        &:hover {
            background: #bee6fd; //#c4e4ff
        }

        & > img {
            width: 5.6rem;
            height: 5.6rem;
            transform: scale(.75);
        }
    }
</style>
