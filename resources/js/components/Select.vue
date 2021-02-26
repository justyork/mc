<template>
    <select :value="value" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block">
        <slot></slot>
    </select>
</template>

<script>
export default {

    props: ['modelValue'],
}
</script>

<style scoped>

</style>
