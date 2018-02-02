<template>
    <div :class="$options.name">
        <section>
            <h1>Main content</h1>
        </section>
        <aside>Main sidebar</aside>
    </div>
</template>

<script>
    export default {
        name: 'TheHomePage',

        // metaInfo: {
        //     title: 'Home page'
        // },

        data() {
            return {
                overlapped: false,
            }
        },

        created() {
//            this.$root.$on('overlay', overlay => this.overlapped = overlay);
        }
    }
</script>

<style lang="postcss">
    .TheHomePage {
        display: contents;
    }
</style>
