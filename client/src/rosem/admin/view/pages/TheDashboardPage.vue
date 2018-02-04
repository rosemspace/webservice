<template>
    <div :class="$options.name">
        <nav>
            <aside></aside>
            <section>Dashboard navigation</section>
        </nav>
        <section>
            <article>
                <header>
                    <h2>Dashboard</h2>
                </header>
                <section class="short-description">
                    <p>Some description</p>
                    <p>Some description 2</p>
                </section>
            </article>
            <article>
                <header>
                    <h2>Dashboard</h2>
                </header>
                <section class="short-description">
                    <p>Some description</p>
                </section>
            </article>
        </section>
        <aside>Main sidebar</aside>
    </div>
</template>

<script>
    export default {
        name: 'TheDashboardPage',

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
    .TheDashboardPage {
        display: contents;
    }
</style>
