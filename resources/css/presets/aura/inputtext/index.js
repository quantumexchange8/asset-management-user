export default {
    root: ({ props, context, parent }) => ({
        class: [
            // Font
            'leading-none',
            'text-sm',
            'font-normal',

            // Flex
            { 'flex-1 w-[1%]': parent.instance.$name == 'InputGroup' },

            // Spacing
            'm-0',
            'w-full',

            // Size
            {
                'py-3 px-3.5': props.size == 'large',
                'py-1.5 px-2': props.size == 'small',
                'py-2 px-3': props.size == null
            },

            // Shape
            { 'rounded-md': parent.instance.$name !== 'InputGroup' },
            { 'first:rounded-l-md rounded-none last:rounded-r-md': parent.instance.$name == 'InputGroup' },
            { 'border-0 border-y border-l last:border-r': parent.instance.$name == 'InputGroup' },
            { 'first:ml-0 -ml-px': parent.instance.$name == 'InputGroup' && !props.showButtons },

            // Colors
            'text-surface-800 dark:text-white/80',
            'placeholder:text-surface-400 dark:placeholder:text-surface-500',
            { 'bg-surface-0 dark:bg-surface-ground': !context.disabled },
            'border',
            { 'border-surface-300 dark:border-surface-700': !props.invalid },

            // Invalid State
            { 'border-red-500 dark:border-red-600 focus:border-red-500 focus:ring-0': props.invalid },

            // States
            {
                'hover:border-surface-400 dark:hover:border-surface-600 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-0 dark:focus:ring-0': !context.disabled && !props.invalid,
                'focus:outline-none focus:outline-offset-0 focus:z-10': !context.disabled,
                'bg-surface-200 dark:bg-surface-700 select-none pointer-events-none cursor-default dark:border-surface-500 disabled:text-surface-500 dark:disabled:text-surface-500': context.disabled
            },

            // Filled State *for FloatLabel
            { filled: (parent.instance?.$name == 'FloatLabel' && context.filled) || (parent.instance?.$parentInstance?.$name == 'FloatLabel' && parent.props.modelValue !== null && parent.props.modelValue?.length !== 0) },

            // Misc
            'appearance-none',
            'transition-colors duration-200'
        ]
    })
};
