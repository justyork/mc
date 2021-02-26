<template>
    <div class="flex">
        <div>
            <h2>Калькулятор</h2>

            <div>
                <jet-input type="text" v-model="searchValue" />

                <div>
                    <ul v-if="searchValue !== '' && filteredValues" class="overflow-y-scroll h-50 py-2">
                        <li v-for="item in filteredValues" @click="selectComponent(item)" class="cursor-pointer hover:text-blue-500"><recipe-name :item="item" /></li>
                    </ul>
                </div>
            </div>

            <div v-if="selectedComponents.length" class="mt-4">
                <h3>Выбрано</h3>
                <div v-for="component in selectedComponents" class="py-2">
                    <recipe-name :item="component"/>
                    <jet-input type="text" class="w-12 h-8 ml-2" v-model="component.count" />
                </div>
            </div>
        </div>
        <div v-if="selectedComponents.length" v-html="calculatedList" class="ml-4">
        </div>
        <ul class="ml-4 bg-white border border-gray-500 p-2" v-if="Object.keys(baseElements).length">
            <li v-for="el in baseElements" class="flex">
                <jet-checkbox v-model="el.checked" />
                <div :className="(el.checked ? 'line-through' : '') + ' ml-2' ">
                    <recipe-name :item="el" /> <span class="text-sm text-gray-500">({{ el.count }})</span>
                </div>
            </li>
        </ul>
    </div>

</template>

<script>
import JetInput from '@/Jetstream/Input'
import JetCheckbox from '@/Jetstream/Checkbox'
import RecipeName from "@/components/RecipeName";
export default {
    components: {
        RecipeName,
        JetInput,
        JetCheckbox
    },
    computed: {
        filteredValues() {
            return this.componentList.filter((el) => {
                let tmpName;
                if (!el.name) {
                    tmpName = `${this.metals[el.metal_id]} ${this.types[el.type_id]}`
                } else {
                    tmpName = el.name;
                }
                return tmpName.toLowerCase().indexOf(this.searchValue.toLowerCase()) !== -1;
            })
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
        name(el) {
            let tier = (el.tier ? ' ('+this.tierList[el.tier]+')' : '');

            if (el.name) {
                return el.name + tier;
            }

            let metal = this.metals[el.metal_id];
            let type = this.types[el.type_id];

            return `${metal} ${type}${tier}`;
        },
        getCalcValue(el, count, lvl) {
            let offset = lvl * 40;
            return `
                <div class="" style="padding-left: ${offset}px">
                     ${this.name(el)} <span class="text-gray-500 text-sm">(${count})</span>
                </div>`;
        },
        addBaseEl(el, count) {
            count = parseFloat(count)
            if (this.baseElements[el.id] === undefined) {
                this.baseElements[el.id] = {count: count, name: el.name, tier: el.tier, metal_id: el.metal_id, type_id: el.type_id}
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
            baseElements: {},
            metals: null,
            types: null
        };
    },
    props: {
        'tierList': Array,
        'componentList': Array,
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
    }
}
</script>

<style scoped>

</style>
