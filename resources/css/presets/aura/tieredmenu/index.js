export default {
    root: {
        class: [
            // Shape
            'rounded-md',

            // Size
            'min-w-[12rem]',
            'p-1',

            // Colors
            'bg-surface-0 dark:bg-surface-900',
            'border border-surface-200 dark:border-surface-700'
        ]
    },
    rootList: {
        class: [
            // Spacings and Shape
            'list-none',
            'flex flex-col',
            'm-0 p-0',
            'outline-none'
        ]
    },
    item: {
        class: 'relative my-[2px] [&:first-child]:mt-0'
    },
    itemContent: ({ context }) => ({
        class: [
            //Shape
            'rounded-[4px]',

            // Colors
            {
                'text-surface-500 dark:text-white/70': !context.focused && !context.active,
                'text-surface-500 dark:text-white/70 bg-surface-200 dark:bg-surface-600/90': context.focused && !context.active,
                'bg-primary-100 text-surface-400 dark:bg-highlight dark:text-highlight-contrast': (context.focused && context.active) || context.active || (!context.focused && context.active)
            },

            // Transitions
            'transition-shadow',
            'duration-200',

            // States
            {
                'hover:bg-surface-100 dark:hover:bg-[rgba(255,255,255,0.03)]': !context.active,
                'hover:bg-primary-100 hover:text-surface-400 dark:hover:bg-highlight': context.active
            },

            // Disabled
            { 'opacity-60 pointer-events-none cursor-default': context.disabled }
        ]
    }),
    itemLink: {
        class: [
            'relative',
            // Flexbox

            'flex',
            'items-center',

            // Spacing
            'py-2',
            'px-3',

            // Misc
            'no-underline',
            'overflow-hidden',
            'cursor-pointer',
            'select-none'
        ]
    },
    itemIcon: {
        class: [
            // Spacing
            'mr-2'
        ]
    },
    itemLabel: {
        class: ['leading-none']
    },
    submenuIcon: {
        class: [
            // Position
            'ml-auto'
        ]
    },
    submenu: {
        class: [
            // Spacing
            'flex flex-col',
            'm-0',
            'p-1',
            'list-none',
            'min-w-[12.5rem]',

            // Shape
            'shadow-none sm:shadow-md',
            'border border-surface-200 dark:border-surface-700',

            // Position
            'static sm:absolute',
            'z-10',

            // Color
            'bg-surface-0 dark:bg-surface-900'
        ]
    },
    separator: {
        class: 'border-t border-surface-200 dark:border-surface-600'
    },
    transition: {
        enterFromClass: 'opacity-0',
        enterActiveClass: 'transition-opacity duration-250'
    }
};
