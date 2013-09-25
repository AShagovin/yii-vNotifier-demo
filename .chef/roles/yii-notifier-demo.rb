name "yii-notifier-demo"

run_list(
	"recipe[yum]",
	"recipe[apache2]",
	"recipe[apache2::vhosts]",
	"recipe[php]"
)