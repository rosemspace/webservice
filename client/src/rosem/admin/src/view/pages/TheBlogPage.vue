<template>
    <div :class="$options.name">
        <nav>
            <aside></aside>
            <section>Blog navigation</section>
        </nav>
        <section>
            <article>
                <header>
                    <h2>Blog</h2>
                </header>
                <section class="short-description">
                    <p>Blog description</p>
                </section>
            </article>
        </section>
        <aside>
            <h2>Contact information: </h2>
            <p>Bla bla bla</p>
        </aside>
    </div>
</template>

<script>
    export default {
        name: 'TheBlogPage',
    }
</script>

<style lang="postcss">
    .TheBlogPage {
        display: contents;
    }
</style>
