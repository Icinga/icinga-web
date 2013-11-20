/*
 * Add backward compatibility for renamed function customColumnPerfdataSanatized
 * @author Markus Frosch <markus.frosch@netways.de>
 */
(function () {
    if (typeof Cronk.grid.ColumnRenderer.customColumnPerfdataSanatized !== 'function') {
        Cronk.grid.ColumnRenderer.customColumnPerfdataSanatized = function (cfg) {
            return Cronk.grid.ColumnRenderer.customColumnPerfdataSanitized(cfg);
        };
    }
})();
