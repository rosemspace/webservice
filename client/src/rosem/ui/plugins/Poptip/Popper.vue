<template>
    <div :class="$options.name">
        <slot />
        <div x-arrow></div>
    </div>
</template>

<script>
    export default {
        name: 'RosemBasePopper',
    }
</script>
