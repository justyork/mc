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
                <div class="flex">
                    <h3 class="text-md font-bold mr-2">Выбрано</h3>
                    <fa icon="sync" @click="selectedComponents = []; baseElements = [];"/>
                </div>
                <div v-for="component in selectedComponents" class="py-2">
                    <recipe-name :item="component"/>
                    <jet-input type="text" class="w-12 h-8 ml-2" v-model="component.count" />
                </div>
            </div>
        </div>
        <div v-if="selectedComponents.length" v-html="calculatedList" class="ml-4">
        </div>
        <div class="ml-4 bg-white border border-gray-500 p-2" v-if="Object.keys(baseElements).length">
            <div v-if="elementTypes.length" class="flex">
                <select v-model="filter.type" class="h-8 mb-4 py-1">
                    <option :value="typeId" v-for="typeId in elementTypes">{{ typeId !== 0 ? types[typeId] : 'Без типа' }}</option>
                </select>
                <fa class="text-red-500 ml-2 mt-2" @click="setFilter('type', false)" icon="times"/>
            </div>
            <ul>
                <li v-for="el in baseItems" class="flex">
                    <div @click="toggleComponent(el)" :className="(isChecked(el) ? 'line-through text-gray-200 hover:text-gray-400' : 'hover:text-red-500') + ' ml-2 cursor-pointer' ">
                        <recipe-name :item="el" />
                        <span class="text-sm text-gray-500">({{ el.count * el.amount }})</span>
                        <span v-if="el.execute_id" class="ml-4 text-xs text-gray-500">{{executes[el.execute_id]}}</span>
                    </div>
                </li>
            </ul>
        </div>
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
                row += this.elementTree(el, Math.ceil(el.count / el.amount));
            })
            return row;
        },
        elementTypes() {
            let typeList = [];
            if (this.baseElements) {
                for (let el in this.baseElements) {
                    if (typeList.indexOf(this.baseElements[el].type_id) === -1) {
                        typeList.push(this.baseElements[el].type_id)
                    }
                }
            }

            return typeList
        },
        baseItems: function () {
            let items = Object.values(this.baseElements);

            if (this.filter.type) {
                items = items.filter(el => el.type_id === this.filter.type)
            }

            items.sort((a, b) => a.type_id < b.type_id)
            return items
        }
    },
    methods: {
        setFilter(name, val) {
            this.filter[name] = val
        },
        selectComponent(el) {
            el.count = 1;
            this.selectedComponents.push(el);
            this.searchValue = '';
        },
        toggleComponent(el) {
            if (this.isChecked(el)) {
                this.checkedComponents.splice(this.checkedComponents.indexOf(el.id), 1)
            } else {
                this.checkedComponents.push(el.id)
            }
        },
        isChecked(el) {
            return this.checkedComponents.indexOf(el.id) !== -1;
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
                row += this.elementTree(component, this.getCorrectCount(component.amount, element.count, count), lvl + 1)
            });

            return row;
        },
        getCorrectCount(min, elementAmount, count) {
            return Math.ceil(count / min) * parseInt(elementAmount);
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
                     ${this.name(el)} <span class="text-gray-500 text-sm">(${count * el.amount})</span>
                </div>`;
        },
        addBaseEl(el, count) {
            count = parseFloat(count)
            if (this.baseElements[el.id] === undefined) {
                this.baseElements[el.id] = {id: el.id, count: count, name: el.name, tier: el.tier, amount: el.amount, param: el.param, metal_id: el.metal_id, type_id: el.type_id, execute_id: el.execute_id}
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
            types: null,
            executes: null,
            selectedTypeList: [],
            filter: {
                type: false
            },
            checkedComponents: []
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
        if (localStorage.getItem('executes')) {
            try {
                this.executes = JSON.parse(localStorage.getItem('executes'));
            } catch(e) {
                localStorage.removeItem('executes');
            }
        }
    }
}
</script>

<style scoped>

</style>
