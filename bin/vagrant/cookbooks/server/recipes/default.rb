require_recipe "apt"
require_recipe "vim"
require_recipe "ntp"
require_recipe "mysql"
require_recipe "memcached"
require_recipe "git"
require_recipe "varnish"

require_recipe "apache2"
require_recipe "apache2::mod_php5"
require_recipe "apache2::mod_ssl"

require_recipe "php"
require_recipe "php::module_apc"
require_recipe "php::module_curl"
require_recipe "php::module_gd"
require_recipe "php::module_mysql"

node.set["apache"]["user"]  = "vagrant"
node.set["apache"]["group"] = "vagrant"

web_app "rax" do
  server_name    "rax.lcl"
  server_aliases "*.rax.lcl"
  docroot        "/home/vagrant/rax/web"
  app_env        "development"
end
