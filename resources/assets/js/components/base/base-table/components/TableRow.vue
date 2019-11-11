<template>
    <tr @click="onClick">
        <table-cell
            v-for="column in visibleColumns"
            :row="row"
            :column="column"
            :key="column.id"
            :responsive-label="column.label"
        ></table-cell>
    </tr>
</template>

<script>
    import TableCell from './TableCell';

    export default {
        props: ['columns', 'row'],

        components: {
            TableCell,
        },

        computed: {
            visibleColumns() {
                return this.columns.filter(column => ! column.hidden);
            }
        },

        methods: {
            onClick(e) {
                this.$emit('rowClick', {
                    e,
                    row: this.row
                })
            }
        }
    };
</script>
