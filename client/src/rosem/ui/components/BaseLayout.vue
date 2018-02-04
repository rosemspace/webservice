<template>
    <main :class="$options.name">
        <slot name="header" :area="`${$options.name}-header`" />
        <slot name="navigation" />
        <slot />
        <slot name="sidebar" />
        <slot name="footer" />
    </main>
</template>

<script>
    export default {
        name: 'BaseLayout',
    }
</script>

<style lang="postcss">
    :root {
        @custom-selector :--header     :nth-child(1);
        @custom-selector :--navigation :nth-child(2);
        @custom-selector :--content    :nth-child(3);
        @custom-selector :--sidebar    :nth-child(4);
        @custom-selector :--footer     :nth-last-child(1);
    }

    .BaseLayout {
        display: grid;
        grid-template:
            "header     header              header"  auto
            "navigation content             sidebar" 1fr
            "footer     footer              footer"  auto
            /1fr        minmax(auto, 800px) 1fr;
        min-height: 100%;

        & > :--header     { grid-area: header;     }
        & > :--navigation { grid-area: navigation; }
        & > :--content    { grid-area: content;    }
        & > :--sidebar    { grid-area: sidebar;    }
        & > :--footer     { grid-area: footer;     }
    }
</style>
