<template>
    <div class="">
        <div class="flex">

            <div class=" p-4 flex-auto">
                <div class="border-b border-gray-300 pb-2 pr-2">
                    <div class="cursor-pointer hover:text-blue-500 " @click="showAddComponent = !showAddComponent">
                    <span v-if="!showAddComponent">
                        <fa icon="plus"  />
                        Add component
                    </span>
                        <span v-else>
                        <fa icon="times"  />
                        Close
                    </span>
                    </div>
                    <add-component v-if="showAddComponent" :tier-list="tierList" :component-list="components.data" />
                </div>

                <div class="flex w-full p-4">
                    <div class="flex-auto">
                        <calculator :tier-list="tierList" :component-list="components.data" />
                    </div>
                </div>
            </div>
            <div  style="max-width: 300px;">
                <h3 class="text-xl font-bold">Added components</h3>
                <ul v-if="components" class="overflow-y-scroll h-full" style="max-height: 600px;">
                    <li v-for="(el) in components.data">{{ el.name }}</li>
                </ul>
            </div>
        </div>
    </div>
</template>

<style scoped>
.btn {
    @apply text-white bg-green-400 px-2 py-1 rounded;
}
</style>

<script>
import AddComponent from "../components/AddComponent";
import CalculatorComponent from "../components/CalculatorComponent";

    export default {
        props: {
            canLogin: Boolean,
            canRegister: Boolean,
            laravelVersion: String,
            phpVersion: String,
            components: Array,
        },
        components: {
            calculator:CalculatorComponent,
            AddComponent,
        },
        data() {
            return {
                tierList: [
                    'None',
                    '8 ULV',
                    '32 LV',
                    '128 MV',
                    '512 HV',
                    '2048 UHV',
                    '8194'
                ],
                showAddComponent: false
            };
        },
    }
</script>
