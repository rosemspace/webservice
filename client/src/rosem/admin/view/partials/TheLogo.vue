<template>
    <div :class="$options.name">
        <router-link :to="{name: '@rosem/admin:admin'}">
            <img src="../../images/logo.png" alt="The logo" />
            <!--<span>Rosem</span>-->
        </router-link>
    </div>
</template>

<script>
    export default {
        name: 'TheLogo',
    }
</script>

<style lang="postcss">
    .TheLogo {
        filter: hue-rotate(10deg);// drop-shadow(0 0 1px #a1bed6);

        & > a {
            clip-path: polygon(0 0, 0 100%, 83% 96%, 96% 83%, 100% 0);
            background: #25547b;
            transform: scale(1.5);
            transition: background-color .4s;

            &:hover {
                background: #bee6fd; //#c4e4ff
            }

            & > img {
                width: 5rem;
                height: 5rem;
                margin-top: .6rem;
                margin-left: .6rem;
                transform: scale(.65);
            }
        }
    }
</style>
