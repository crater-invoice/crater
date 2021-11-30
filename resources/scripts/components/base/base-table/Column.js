import { pick } from './helpers';

export default class Column {
  constructor(columnObject) {
    const properties = pick(columnObject, [
      'key', 'label', 'thClass', 'tdClass', 'sortBy', 'sortable', 'hidden', 'dataType'
    ]);

    for (const property in properties) {
      this[property] = columnObject[property];
    }

    if (!properties['dataType']) {
      this['dataType'] = 'string'
    }

    if (properties['sortable'] === undefined) {
      this['sortable'] = true
    }
  }

  getFilterFieldName() {
    return this.filterOn || this.key;
  }

  isSortable() {
    return this.sortable;
  }

  getSortPredicate(sortOrder, allColumns) {
    const sortFieldName = this.getSortFieldName();

    const sortColumn = allColumns.find(column => column.key === sortFieldName);

    const dataType = sortColumn.dataType;

    if (dataType.startsWith('date') || dataType === 'numeric') {

      return (row1, row2) => {
        const value1 = row1.getSortableValue(sortFieldName);
        const value2 = row2.getSortableValue(sortFieldName);

        if (sortOrder === 'desc') {
          return value2 < value1 ? -1 : 1;
        }

        return value1 < value2 ? -1 : 1;
      };
    }

    return (row1, row2) => {
      const value1 = row1.getSortableValue(sortFieldName);
      const value2 = row2.getSortableValue(sortFieldName);

      if (sortOrder === 'desc') {
        return value2.localeCompare(value1);
      }

      return value1.localeCompare(value2);
    };
  }

  getSortFieldName() {
    return this.sortBy || this.key;
  }
}
