include_recipe "apache2"

web_app "yii-notifier" do
  server_name "yii-notifier.local"
  server_aliases ["www.yii-notifier.local"]
  allow_override "all"
  docroot "/vagrant/www/"
end
