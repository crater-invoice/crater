import Vue, { VNode } from 'vue';

declare class BaseMultiselect extends Vue {
    modelValue?: any;
    value?: any;
    mode: 'single' | 'multiple' | 'tags';
    options?: any[];
    searchable?: boolean;
    valueProp?: string;
    trackBy?: string;
    label?: string;
    placeholder?: string | null;
    multipleLabel?: any; // Function
    disabled?: boolean;
    max?: number;
    limit?: number;
    loading?: boolean;
    id?: string;
    caret?: boolean;
    maxHeight?: string | number;
    noOptionsText?: string;
    noResultsText?: string;
    canDeselect?: boolean;
    canClear?: boolean;
    clearOnSearch?: boolean;
    clearOnSelect?: boolean;
    delay?: number;
    filterResults?: boolean;
    minChars?: number;
    resolveOnLoad?: boolean;
    appendNewTag?: boolean;
    createTag?: boolean;
    addTagOn?: string[];
    hideSelected?: boolean;
    showOptions?: boolean;
    object?: boolean;
    required?: boolean;
    openDirection?: 'top' | 'bottom';
    nativeSupport?: boolean;
    classes?: object;
    strict?: boolean;
    closeOnSelect?: boolean;
    autocomplete?: string;
    groups: boolean;
    groupLabel: string;
    groupOptions: string;
    groupHideEmpty: boolean;
    groupSelect: boolean;
    inputType: string;

    $emit(eventName: 'change', e: { originalEvent: Event, value: any }): this;
    $emit(eventName: 'select', e: { originalEvent: Event, value: any, option: any }): this;
    $emit(eventName: 'deselect', e: { originalEvent: Event, value: any, option: any }): this;
    $emit(eventName: 'remove', e: { originalEvent: Event, value: any, option: any }): this;
    $emit(eventName: 'search-change', e: { originalEvent: Event, query: string }): this;
    $emit(eventName: 'tag', e: { originalEvent: Event, query: string }): this;
    $emit(eventName: 'paste', e: { originalEvent: Event }): this;
    $emit(eventName: 'open'): this;
    $emit(eventName: 'close'): this;
    $emit(eventName: 'clear'): this;

    $slots: {
        placeholder: VNode[];
        afterlist: VNode[];
        beforelist: VNode[];
        list: VNode[];
        multiplelabel: VNode[];
        singlelabel: VNode[];
        option: VNode[];
        groupLabel: VNode[];
        tag: VNode[];
    };
}

export default BaseMultiselect;
