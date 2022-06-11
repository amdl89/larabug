const fetchState = {
    loading: "Loading",
    loaded: "Loaded",
    error: "Error",
};

export default {
    data() {
        return {
            currFetchState: fetchState.loaded,
        };
    },
    computed: {
        fetchState() {
            return fetchState;
        },
    },
    methods: {
        setCurrFetchState(val) {
            this.currFetchState = val;
        },
    },
};
