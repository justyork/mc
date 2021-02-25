<template>
    <div class="flex">
        <div>
            <h2>Calculate</h2>

            <div>
                <jet-input type="text" v-model="searchValue" />

                <div>
                    <ul v-if="searchValue !== '' && filteredValues" class="overflow-y-scroll h-50 py-2">
                        <li v-for="item in filteredValues" @click="selectComponent(item)" class="cursor-pointer hover:text-blue-500">{{ getName(item) }}</li>
                    </ul>
                </div>
            </div>

            <div v-if="selectedComponents.length">
                <h3>Selected</h3>
                <div v-for="component in selectedComponents" class="py-2">
                    {{ getName(component) }}
                    <jet-input type="text" class="w-20 h-8" v-model="component.count" />
                </div>
            </div>
        </div>
        <div v-if="selectedComponents.length" v-html="calculatedList" class="ml-4">
        </div>
        <ul class="ml-4 bg-white border border-gray-500 p-2" v-if="Object.keys(baseElements).length">
            <li v-for="el in baseElements" class="flex">
                <jet-checkbox v-model="el.checked" />
                <div :className="(el.checked ? 'line-through' : '') + ' ml-2' ">
                    {{ getName(el) }} <span class="text-sm text-gray-500">({{ el.count }})</span>
                </div>
            </li>
        </ul>
    </div>

</template>

<script>
import JetInput from '@/Jetstream/Input'
import JetCheckbox from '@/Jetstream/Checkbox'
export default {
    components: {
        JetInput,
        JetCheckbox
    },
    computed: {
        filteredValues() {
            return this.componentList.filter((el) => el.name.toLowerCase().indexOf(this.searchValue.toLowerCase()) !== -1)
        },
        calculatedList() {
            let row = '';
            this.baseElements = {};
            this.selectedComponents.forEach((el) => {
                row += this.elementTree(el, el.count);
            })
            return row;
        },
    },
    methods: {
        getName(el) {
            return el.name + (el.tier ? ' ('+this.tierList[el.tier]+')' : '');
        },
        selectComponent(el) {
            el.count = 1;
            this.selectedComponents.push(el);
            this.searchValue = '';
        },
        elementTree(el, count = 1, lvl = 0) {
            if (el.elements.length === 0) {
                this.addBaseEl(el, count)
                return this.getCalcValue(el, count, lvl);
            }

            this.addBaseEl(el, count)
            let row = this.getCalcValue(el, count, lvl);
            el.elements.forEach((element) => {
                let index = this.componentList.findIndex((comp) => comp.id === element.component_id);
                let component = this.componentList[index];
                row += this.elementTree(component, parseInt(count) * parseFloat(element.count), lvl + 1)
            });

            return row;
        },
        getCalcValue(el, count, lvl) {
            let offset = lvl * 40;
            return `
                <div class="" style="padding-left: ${offset}px">
                     ${this.getName(el)} <span class="text-gray-500 text-sm">(${count})</span>
                </div>
            `;
        },
        addBaseEl(el, count) {
            count = parseFloat(count)
            if (this.baseElements[el.id] === undefined) {
                this.baseElements[el.id] = {count: count, name: el.name, tier: el.tier}
            } else {
                this.baseElements[el.id].count += count;
            }
        }
    },
    data() {
        return {
            searchValue: '',
            selectedComponents: [],
            levels: {},
            baseElements: {}
        };
    },
    props: {
        'tierList': Array,
        'componentList': Array,
    },
}
</script>

<style scoped>

</style>
