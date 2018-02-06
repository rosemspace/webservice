<template>
    <div :class="$options.name">
        <slot />
        <div x-arrow></div>
    </div>
</template>

<script>
    export default {
        name: 'RosemPopupTooltip',
    }
</script>

<style lang="postcss">
    .RosemPopupTooltip {
        background: white;
    }
</style>
