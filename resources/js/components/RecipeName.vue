<template>
    <span>
        {{name()}}
    </span>
</template>

<script>
export default {
    props: {
        item: Object,
    },
    data() {
        return {
            tierList: null,
            metals: null,
            types: null
        }
    },
    methods: {
        name() {
            let tier = (this.item.tier ? ' ('+this.tierList[this.item.tier]+')' : '');

            if (this.item.name) {
                return this.item.name + tier;
            }

            let metal = this.metals[this.item.metal_id];
            let type = this.types[this.item.type_id];

            return `${metal} ${type}${tier}`;
        }
    },
    created() {
        if (localStorage.getItem('metals')) {
            try {
                this.metals = JSON.parse(localStorage.getItem('metals'));
            } catch(e) {
                localStorage.removeItem('metals');
            }

        }
        if (localStorage.getItem('types')) {
            try {
                this.types = JSON.parse(localStorage.getItem('types'));
            } catch(e) {
                localStorage.removeItem('types');
            }
        }
        if (localStorage.getItem('tierList')) {
            try {
                this.tierList = JSON.parse(localStorage.getItem('tierList'));
            } catch(e) {
                localStorage.removeItem('tierList');
            }
        }
    },
}
</script>

<style scoped>

</style>
